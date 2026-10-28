<?php

namespace App\Livewire\Developer;

use App\Enums\RequestStatus;
use App\Mail\DocumentCommentAddedMail;
use App\Mail\EthicsRequestStatusUpdatedMail;
use App\Mail\EthicsRequestSubmittedMail;
use App\Models\BugReport;
use App\Models\Comment;
use App\Models\ControlNumberCounter;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\EthicsRequest;
use App\Models\RequestDocument;
use App\Models\SystemAnnouncement;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class DevPanel extends Component
{
    public string $activeTab = 'system';

    // Resend Email Diagnostics State
    public string $testEmailRecipient = '';

    // Control Number Editor State
    public ?int $selectedCounterId = null;

    public int $newSequence = 0;

    public bool $showConfirmModal = false;

    // Site-Wide Announcement State
    public string $announcementType = 'maintenance';

    public string $announcementTitle = '';

    public string $announcementMessage = '';

    public bool $announcementIsActive = true;

    // User & Access Control State
    public string $userSearch = '';

    public string $newUserEmail = '';

    public string $newUserName = '';

    public string $newUserRole = 'admin';

    public bool $newUserIsDeveloper = false;

    public bool $newUserIsChairperson = false;

    public function mount()
    {
        if (! auth()->user()->is_developer) {
            abort(403, 'Developer panel access restricted.');
        }

        $active = SystemAnnouncement::where('is_active', true)->first();
        if ($active) {
            $this->announcementType = $active->type;
            $this->announcementTitle = $active->title;
            $this->announcementMessage = $active->message;
            $this->announcementIsActive = $active->is_active;
        } else {
            $this->applyPreset('maintenance');
        }
    }

    public function applyPreset(string $type): void
    {
        $this->announcementType = $type;
        switch ($type) {
            case 'maintenance':
                $this->announcementTitle = 'Scheduled System Maintenance';
                $this->announcementMessage = 'The DEMO Ethics Review Board Portal is currently undergoing scheduled system updates. Document uploads and approvals may experience brief delays.';
                break;
            case 'down':
                $this->announcementTitle = 'Temporary System Service Outage';
                $this->announcementMessage = 'Our document processing service is currently experiencing an unexpected outage. Technical engineers are actively resolving the issue.';
                break;
            case '404_issue':
                $this->announcementTitle = '404 Document Link / Route Warning';
                $this->announcementMessage = 'Certain template download and preview links are temporarily returning 404 Not Found while server migration completes.';
                break;
            case '402_issue':
                $this->announcementTitle = '402 Ethics Review Fee System Alert';
                $this->announcementMessage = 'The DEMO Cashier fee validation portal is currently offline. Please retain your original physical receipt for manual verification.';
                break;
            case 'info':
            default:
                $this->announcementTitle = 'Important Portal Notice';
                $this->announcementMessage = 'Please ensure all submitted manuscripts comply with the 2026 DEMO Ethics Review Board guidelines before final submission.';
                break;
        }
    }

    public function saveAnnouncement(): void
    {
        $this->validate([
            'announcementType' => 'required|string',
            'announcementTitle' => 'required|string|max:255',
            'announcementMessage' => 'required|string|max:1000',
        ]);

        // Deactivate old announcements if active
        if ($this->announcementIsActive) {
            SystemAnnouncement::query()->update(['is_active' => false]);
        }

        SystemAnnouncement::create([
            'type' => $this->announcementType,
            'title' => $this->announcementTitle,
            'message' => $this->announcementMessage,
            'is_active' => $this->announcementIsActive,
            'created_by' => auth()->id(),
        ]);

        session()->flash('success', 'Site-wide announcement saved and updated successfully.');
    }

    public function toggleAnnouncementStatus(int $id): void
    {
        $announcement = SystemAnnouncement::findOrFail($id);
        $newStatus = ! $announcement->is_active;

        if ($newStatus) {
            SystemAnnouncement::query()->update(['is_active' => false]);
        }

        $announcement->update(['is_active' => $newStatus]);
        session()->flash('success', 'Announcement status updated.');
    }

    public function deleteAnnouncement(int $id): void
    {
        SystemAnnouncement::destroy($id);
        session()->flash('success', 'Announcement deleted.');
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function openEditCounter(int $counterId, int $currentSequence)
    {
        $this->selectedCounterId = $counterId;
        $this->newSequence = $currentSequence;
        $this->showConfirmModal = true;
    }

    public function confirmUpdateCounter()
    {
        $counter = ControlNumberCounter::findOrFail($this->selectedCounterId);
        $oldSeq = $counter->last_sequence;
        $counter->update(['last_sequence' => $this->newSequence]);

        $this->showConfirmModal = false;
        session()->flash('success', "Control number counter updated from {$oldSeq} to {$this->newSequence}.");
    }

    public function designateChairperson(int $userId): void
    {
        if (! auth()->user() || ! auth()->user()->is_developer) {
            abort(403, 'Developer panel access restricted.');
        }

        $user = User::findOrFail($userId);

        DB::transaction(function () use ($user) {
            User::where('is_chairperson', true)->update(['is_chairperson' => false]);
            $user->update(['is_chairperson' => true]);
        });

        session()->flash('success', "{$user->name} designated as ERB Chairperson.");
    }

    public function removeChairperson(): void
    {
        if (! auth()->user() || ! auth()->user()->is_developer) {
            abort(403, 'Developer panel access restricted.');
        }

        User::where('is_chairperson', true)->update(['is_chairperson' => false]);
        session()->flash('success', 'Chairperson designation cleared.');
    }

    public function provisionUser(): void
    {
        if (! auth()->user() || ! auth()->user()->is_developer) {
            abort(403, 'Developer panel access restricted.');
        }

        $this->validate([
            'newUserEmail' => 'required|email|max:255',
            'newUserName' => 'nullable|string|max:255',
            'newUserRole' => 'required|in:admin,committee,student',
        ]);

        $email = strtolower(trim($this->newUserEmail));

        $user = User::firstOrNew(['email' => $email]);
        if (! $user->exists) {
            $name = $this->newUserName ?: explode('@', $email)[0];
            $user->name = ucwords(str_replace(['.', '_', '-'], ' ', $name));
            $user->is_active = true;
        }

        $user->is_developer = $this->newUserIsDeveloper;
        if ($this->newUserIsChairperson) {
            User::where('is_chairperson', true)->update(['is_chairperson' => false]);
            $user->is_chairperson = true;
        }
        $user->save();

        Role::firstOrCreate(['name' => $this->newUserRole, 'guard_name' => 'web']);
        $user->syncRoles([$this->newUserRole]);

        $this->newUserEmail = '';
        $this->newUserName = '';
        $this->newUserIsDeveloper = false;
        $this->newUserIsChairperson = false;
        $this->resetErrorBag();

        session()->flash('success', "Account for {$email} configured with role '{$this->newUserRole}'.");
    }

    public function toggleDeveloper(int $userId): void
    {
        if (! auth()->user() || ! auth()->user()->is_developer) {
            abort(403, 'Developer panel access restricted.');
        }

        $user = User::findOrFail($userId);
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot revoke your own developer privileges.');

            return;
        }

        $user->update(['is_developer' => ! $user->is_developer]);
        session()->flash('success', "Developer status updated for {$user->name}.");
    }

    public function assignUserRole(int $userId, string $role): void
    {
        if (! auth()->user() || ! auth()->user()->is_developer) {
            abort(403, 'Developer panel access restricted.');
        }

        $user = User::findOrFail($userId);
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        $user->syncRoles([$role]);
        session()->flash('success', "Role for {$user->name} updated to {$role}.");
    }

    public function toggleUserActive(int $userId): void
    {
        if (! auth()->user() || ! auth()->user()->is_developer) {
            abort(403, 'Developer panel access restricted.');
        }

        $user = User::findOrFail($userId);
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot deactivate your own account.');

            return;
        }

        $user->update(['is_active' => ! $user->is_active]);
        session()->flash('success', "Account status updated for {$user->name}.");
    }

    public function render()
    {
        // 1. System Info
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug') ? 'ENABLED' : 'DISABLED',
            'db_connection' => config('database.default'),
            'app_url' => config('app.url'),
        ];

        // 2. Queue Info
        $failedJobs = Schema::hasTable('failed_jobs')
            ? DB::table('failed_jobs')->orderBy('id', 'desc')->limit(10)->get()
            : collect();

        $pendingJobs = Schema::hasTable('jobs')
            ? DB::table('jobs')->count()
            : 0;

        // 3. Control Number Counters
        $counters = ControlNumberCounter::all();

        // 4. Error Logs
        $logPath = storage_path('logs/laravel.log');
        $logContent = File::exists($logPath) ? File::get($logPath) : 'No log file found.';
        $recentLogs = array_slice(explode("\n", $logContent), -50);

        // 5. System Announcements
        $announcements = SystemAnnouncement::with('creator')->latest()->get();

        // 6. Bug Reports
        $bugReportsQuery = BugReport::with('user')->latest();
        if ($this->bugStatusFilter !== 'all') {
            $bugReportsQuery->where('status', $this->bugStatusFilter);
        }
        $bugReports = $bugReportsQuery->get();
        $openBugCount = BugReport::where('status', 'open')->count();
        $selectedBugReport = $this->selectedBugReportId ? BugReport::with('user')->find($this->selectedBugReportId) : null;

        // 7. Temporary Uploads Health Metrics
        $tmpDirs = [storage_path('app/private/livewire-tmp'), storage_path('app/livewire-tmp')];
        $tmpFileCount = 0;
        $tmpBytes = 0;
        foreach ($tmpDirs as $dir) {
            if (File::exists($dir) && is_readable($dir)) {
                try {
                    foreach (File::allFiles($dir) as $f) {
                        $tmpFileCount++;
                        $tmpBytes += $f->getSize();
                    }
                } catch (\Throwable) {
                    // Ignore unreadable files/directories during metric aggregation
                }
            }
        }
        $tmpMB = round($tmpBytes / (1024 * 1024), 2);

        // 8. ERB Chairperson State
        $currentChairperson = User::where('is_chairperson', true)->first();
        $eligibleChairpersons = User::where('is_active', true)->orderBy('name')->get();

        // 9. All Users for Developer Access Management
        $usersQuery = User::query();
        if ($this->userSearch) {
            $usersQuery->where(function ($q) {
                $q->where('name', 'like', '%'.$this->userSearch.'%')
                    ->orWhere('email', 'like', '%'.$this->userSearch.'%');
            });
        }
        $allUsers = $usersQuery->orderBy('name')->get();
        $allRoles = Role::all();

        return view('livewire.developer.dev-panel', [
            'systemInfo' => $systemInfo,
            'failedJobs' => $failedJobs,
            'pendingJobs' => $pendingJobs,
            'counters' => $counters,
            'recentLogs' => implode("\n", $recentLogs),
            'announcements' => $announcements,
            'bugReports' => $bugReports,
            'openBugCount' => $openBugCount,
            'selectedBugReport' => $selectedBugReport,
            'tmpFileCount' => $tmpFileCount,
            'tmpMB' => $tmpMB,
            'currentChairperson' => $currentChairperson,
            'eligibleChairpersons' => $eligibleChairpersons,
            'allUsers' => $allUsers,
            'allRoles' => $allRoles,
        ])->layout('layouts.app', ['header' => 'Developer Operational Maintenance Panel']);
    }

    public function purgeOrphanUploads(): void
    {
        Artisan::call('app:clean-orphan-uploads', ['--hours' => 0]);
        $output = Artisan::output();
        session()->flash('success', trim($output) ?: 'Temporary upload storage purged.');
    }

    public function sendTestResendEmail(): void
    {
        if (! auth()->user() || ! auth()->user()->is_developer) {
            abort(403, 'Developer panel access restricted.');
        }

        $this->validate([
            'testEmailRecipient' => 'required|email',
        ]);

        $mockRequest = new EthicsRequest([
            'id' => 99999,
            'title' => 'Sample Research Protocol: AI Ethics and Clinical Data Governance',
            'control_number' => 'ERBTEST_'.date('Y').'_99999',
            'status' => RequestStatus::Submitted,
        ]);
        $mockRequest->student = new User([
            'name' => 'Test Researcher',
            'email' => $this->testEmailRecipient,
        ]);
        $mockRequest->department = new Department([
            'name' => 'School of Nursing & Allied Health Sciences',
        ]);

        Mail::to($this->testEmailRecipient)->send(new EthicsRequestSubmittedMail($mockRequest));

        session()->flash('success', 'Test email successfully dispatched to '.$this->testEmailRecipient.' via Resend.');
    }

    public function sendAllTestResendEmails(): void
    {
        if (! auth()->user() || ! auth()->user()->is_developer) {
            abort(403, 'Developer panel access restricted.');
        }

        $this->validate([
            'testEmailRecipient' => 'required|email',
        ]);

        $recipient = $this->testEmailRecipient;

        // --- Shared mocks ---
        $student = new User([
            'name' => 'Test Researcher',
            'email' => $recipient,
        ]);

        $department = new Department([
            'name' => 'School of Nursing & Allied Health Sciences',
        ]);

        // 1) Submission acknowledgment (Submitted)
        $mockSubmitted = new EthicsRequest([
            'id' => 99999,
            'title' => 'Sample Research Protocol: AI Ethics and Clinical Data Governance',
            'control_number' => 'ERBTEST_'.date('Y').'_99999',
            'status' => RequestStatus::Submitted,
        ]);
        $mockSubmitted->setRelation('student', $student);
        $mockSubmitted->setRelation('department', $department);
        // fallback for views using dynamic property
        $mockSubmitted->student = $student;
        $mockSubmitted->department = $department;

        // 2) Status update (Approved with remarks to demo green badge + remarks card)
        $mockApproved = new EthicsRequest([
            'id' => 99999,
            'title' => 'Sample Research Protocol: AI Ethics and Clinical Data Governance',
            'control_number' => 'ERBTEST_'.date('Y').'_99999',
            'status' => RequestStatus::Approved,
        ]);
        $mockApproved->setRelation('student', $student);
        $mockApproved->setRelation('department', $department);
        $mockApproved->student = $student;
        $mockApproved->department = $department;
        $remarks = 'Congratulations — your protocol has been approved. Certificate will be available in the portal. Retain this email for compliance records.';

        // 3) Reviewer comment (RequestDocument + Comment)
        $mockRequestForDoc = new EthicsRequest([
            'id' => 99999,
            'title' => 'Sample Research Protocol: AI Ethics and Clinical Data Governance',
            'control_number' => 'ERBTEST_'.date('Y').'_99999',
            'status' => RequestStatus::UnderReview,
        ]);
        $mockRequestForDoc->setRelation('student', $student);
        $mockRequestForDoc->setRelation('department', $department);
        $mockRequestForDoc->student = $student;
        $mockRequestForDoc->department = $department;

        $docType = new DocumentType([
            'name' => 'Informed Consent Form',
        ]);

        $mockDocument = new RequestDocument([
            'id' => 88888,
            'ethics_request_id' => 99999,
        ]);
        $mockDocument->setRelation('ethicsRequest', $mockRequestForDoc);
        $mockDocument->setRelation('documentType', $docType);
        // compatibility for ->ethicsRequest dynamic access
        $mockDocument->ethicsRequest = $mockRequestForDoc;
        $mockDocument->documentType = $docType;

        $mockComment = new Comment([
            'comment_text' => "Sample reviewer feedback: Please clarify the data retention period in Section 3.2 and confirm that participant identifiers will be anonymized before storage. Kindly upload a revised version when ready.\n\n— DEMO ERB Review Panel",
        ]);
        $reviewer = new User([
            'name' => 'Demo Chair — ERB Reviewer',
            'email' => 'reviewer@example.test',
        ]);
        $mockComment->setRelation('commenter', $reviewer);
        $mockComment->commenter = $reviewer;
        $mockComment->created_at = now();

        // Dispatch all three via Resend (synchronously)
        Mail::to($recipient)->send(new EthicsRequestSubmittedMail($mockSubmitted));
        Mail::to($recipient)->send(new EthicsRequestStatusUpdatedMail($mockApproved, $remarks));
        Mail::to($recipient)->send(new DocumentCommentAddedMail($mockDocument, $mockComment));

        session()->flash('success', 'All 3 test emails dispatched to '.$recipient.' via Resend: Submission Acknowledgment, Decision Update (Approved), and Reviewer Feedback.');
    }

    public string $bugStatusFilter = 'all';

    public ?int $selectedBugReportId = null;

    public bool $showTelemetryModal = false;

    public function markBugStatus(int $id, string $status): void
    {
        $bug = BugReport::findOrFail($id);
        $bug->update(['status' => $status]);
        session()->flash('success', "Bug report #{$id} status changed to ".ucfirst(str_replace('_', ' ', $status)).'.');
    }

    public function deleteBugReport(int $id): void
    {
        BugReport::destroy($id);
        if ($this->selectedBugReportId === $id) {
            $this->showTelemetryModal = false;
        }
        session()->flash('success', "Bug report #{$id} deleted.");
    }

    public function inspectTelemetry(int $id): void
    {
        $this->selectedBugReportId = $id;
        $this->showTelemetryModal = true;
    }
}
