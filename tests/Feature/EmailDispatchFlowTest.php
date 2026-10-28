<?php

namespace Tests\Feature;

use App\Enums\DocumentStatus;
use App\Enums\RequestStatus;
use App\Livewire\CreateEthicsRequest;
use App\Livewire\DocumentViewer;
use App\Livewire\RequestDetail;
use App\Mail\DocumentCommentAddedMail;
use App\Mail\EthicsRequestStatusUpdatedMail;
use App\Mail\EthicsRequestSubmittedMail;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\EthicsRequest;
use App\Models\RequestDocument;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EmailDispatchFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'committee', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    }

    public function test_submitting_ethics_request_dispatches_acknowledgment_email(): void
    {
        Mail::fake();
        Storage::fake('local');

        $student = User::factory()->create(['email' => 'student@example.test', 'is_active' => true]);
        $student->assignRole('student');
        $dept = Department::firstOrCreate(['name' => 'School of Pharmacy'], ['is_active' => true]);

        $test = Livewire::actingAs($student)
            ->test(CreateEthicsRequest::class)
            ->set('title', 'Pharmacology Research Protocol')
            ->set('department_id', $dept->id)
            ->set('research_domain', 'health');

        $requiredTypes = DocumentType::where('is_active', true)->where('is_required', true)->get();
        foreach ($requiredTypes as $type) {
            $test->set('uploads.'.$type->id, UploadedFile::fake()->create("doc_{$type->id}.pdf", 500, 'application/pdf'));
        }

        $test->set('privacyConsentAccepted', true)
            ->call('confirmSubmission')
            ->assertHasNoErrors();

        Mail::assertSent(EthicsRequestSubmittedMail::class, function ($mail) use ($student) {
            return $mail->hasTo($student->email);
        });
    }

    public function test_adding_document_comment_dispatches_notification_email_to_student(): void
    {
        Mail::fake();

        $student = User::factory()->create(['email' => 'student@example.test', 'is_active' => true]);
        $student->assignRole('student');
        $reviewer = User::factory()->create(['email' => 'reviewer@example.test', 'is_active' => true]);
        $reviewer->assignRole('committee');

        $dept = Department::create(['name' => 'School of Education', 'code' => 'SE', 'is_active' => true]);
        $request = EthicsRequest::create([
            'control_number' => 'ERB2026_00099',
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Education Ethics Survey',
            'status' => RequestStatus::UnderReview,
        ]);
        $docType = DocumentType::create(['name' => 'Informed Consent', 'code' => 'icf', 'is_required' => true, 'sort_order' => 1, 'is_active' => true]);
        $reqDoc = RequestDocument::create([
            'ethics_request_id' => $request->id,
            'document_type_id' => $docType->id,
            'status' => DocumentStatus::UnderReview,
        ]);
        $reqDoc->versions()->create([
            'version_number' => 1,
            'file_path' => 'documents/icf.pdf',
            'file_name' => 'icf.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'uploaded_by' => $student->id,
        ]);

        Livewire::actingAs($reviewer)
            ->test(DocumentViewer::class, ['requestId' => $request->id, 'documentId' => $reqDoc->id])
            ->set('newComment', 'Please update the data retention clause.')
            ->call('addComment');

        Mail::assertSent(DocumentCommentAddedMail::class, function ($mail) use ($student) {
            return $mail->hasTo($student->email);
        });
    }

    public function test_student_adding_comment_does_not_dispatch_notification_email_to_student(): void
    {
        Mail::fake();

        $student = User::factory()->create(['email' => 'student@example.test', 'is_active' => true]);
        $student->assignRole('student');

        $dept = Department::create(['name' => 'School of Education', 'code' => 'SE', 'is_active' => true]);
        $request = EthicsRequest::create([
            'control_number' => 'ERB2026_00099',
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Education Ethics Survey',
            'status' => RequestStatus::UnderReview,
        ]);
        $docType = DocumentType::create(['name' => 'Informed Consent', 'code' => 'icf', 'is_required' => true, 'sort_order' => 1, 'is_active' => true]);
        $reqDoc = RequestDocument::create([
            'ethics_request_id' => $request->id,
            'document_type_id' => $docType->id,
            'status' => DocumentStatus::UnderReview,
        ]);
        $reqDoc->versions()->create([
            'version_number' => 1,
            'file_path' => 'documents/icf.pdf',
            'file_name' => 'icf.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'uploaded_by' => $student->id,
        ]);

        Livewire::actingAs($student)
            ->test(DocumentViewer::class, ['requestId' => $request->id, 'documentId' => $reqDoc->id])
            ->set('newComment', 'I have updated the attachment.')
            ->call('addComment');

        Mail::assertNotSent(DocumentCommentAddedMail::class);
    }

    public function test_requesting_revision_dispatches_status_updated_email_to_student(): void
    {
        Mail::fake();

        $student = User::factory()->create(['email' => 'student@example.test', 'is_active' => true]);
        $student->assignRole('student');
        $reviewer = User::factory()->create(['email' => 'reviewer@example.test', 'is_active' => true]);
        $reviewer->assignRole('committee');

        $dept = Department::create(['name' => 'School of Education', 'code' => 'SE', 'is_active' => true]);
        $request = EthicsRequest::create([
            'control_number' => 'ERB2026_00099',
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Education Ethics Survey',
            'status' => RequestStatus::UnderReview,
        ]);
        $docType = DocumentType::create(['name' => 'Informed Consent', 'code' => 'icf', 'is_required' => true, 'sort_order' => 1, 'is_active' => true]);
        $reqDoc = RequestDocument::create([
            'ethics_request_id' => $request->id,
            'document_type_id' => $docType->id,
            'status' => DocumentStatus::UnderReview,
        ]);

        app(DocumentService::class)->requestRevision($reqDoc, $reviewer);

        Mail::assertSent(EthicsRequestStatusUpdatedMail::class, function ($mail) use ($student) {
            return $mail->hasTo($student->email) && $mail->ethicsRequest->status === RequestStatus::RevisionsRequested;
        });
    }

    public function test_approving_request_dispatches_status_updated_email_to_student(): void
    {
        Mail::fake();
        Storage::fake('local');

        $student = User::factory()->create(['email' => 'student@example.test', 'is_active' => true]);
        $student->assignRole('student');
        $reviewer = User::factory()->create(['email' => 'reviewer@example.test', 'is_active' => true]);
        $reviewer->assignRole('committee');

        $dept = Department::create(['name' => 'School of Education', 'code' => 'SE', 'is_active' => true]);
        $request = EthicsRequest::create([
            'control_number' => 'ERB2026_00099',
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Education Ethics Survey',
            'status' => RequestStatus::UnderReview,
        ]);
        $docType = DocumentType::create(['name' => 'Informed Consent', 'code' => 'icf', 'is_required' => true, 'sort_order' => 1, 'is_active' => true]);
        $reqDoc = RequestDocument::create([
            'ethics_request_id' => $request->id,
            'document_type_id' => $docType->id,
            'status' => DocumentStatus::Approved,
        ]);
        $reqDoc->versions()->create([
            'version_number' => 1,
            'file_path' => 'documents/icf.pdf',
            'file_name' => 'icf.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'uploaded_by' => $student->id,
        ]);

        $signature = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        Livewire::actingAs($reviewer)
            ->test(RequestDetail::class, ['id' => $request->id])
            ->call('approveWithSignature', $signature);

        Mail::assertSent(EthicsRequestStatusUpdatedMail::class, function ($mail) use ($student) {
            return $mail->hasTo($student->email) && $mail->ethicsRequest->status === RequestStatus::Approved;
        });
    }

    public function test_adding_document_reply_by_reviewer_dispatches_notification_email_to_student(): void
    {
        Mail::fake();

        $student = User::factory()->create(['email' => 'student@example.test', 'is_active' => true]);
        $student->assignRole('student');
        $reviewer = User::factory()->create(['email' => 'reviewer@example.test', 'is_active' => true]);
        $reviewer->assignRole('committee');

        $dept = Department::create(['name' => 'School of Education', 'code' => 'SE', 'is_active' => true]);
        $request = EthicsRequest::create([
            'control_number' => 'ERB2026_00099',
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Education Ethics Survey',
            'status' => RequestStatus::UnderReview,
        ]);
        $docType = DocumentType::create(['name' => 'Informed Consent', 'code' => 'icf', 'is_required' => true, 'sort_order' => 1, 'is_active' => true]);
        $reqDoc = RequestDocument::create([
            'ethics_request_id' => $request->id,
            'document_type_id' => $docType->id,
            'status' => DocumentStatus::UnderReview,
        ]);
        $version = $reqDoc->versions()->create([
            'version_number' => 1,
            'file_path' => 'documents/icf.pdf',
            'file_name' => 'icf.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'uploaded_by' => $student->id,
        ]);
        $parentComment = $version->comments()->create([
            'commenter_id' => $student->id,
            'comment_text' => 'Here is my initial comment.',
            'resolved' => false,
        ]);

        Livewire::actingAs($reviewer)
            ->test(DocumentViewer::class, ['requestId' => $request->id, 'documentId' => $reqDoc->id])
            ->set('replyText', 'Here is the committee reply.')
            ->call('addReply', $parentComment->id);

        Mail::assertSent(DocumentCommentAddedMail::class, function ($mail) use ($student) {
            return $mail->hasTo($student->email);
        });
    }

    public function test_missing_student_email_does_not_throw_exception(): void
    {
        Mail::fake();

        $student = User::factory()->create(['email' => '', 'is_active' => true]);
        $student->assignRole('student');
        $reviewer = User::factory()->create(['email' => 'reviewer@example.test', 'is_active' => true]);
        $reviewer->assignRole('committee');

        $dept = Department::create(['name' => 'School of Education', 'code' => 'SE', 'is_active' => true]);
        $request = EthicsRequest::create([
            'control_number' => 'ERB2026_00099',
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'title' => 'Education Ethics Survey',
            'status' => RequestStatus::UnderReview,
        ]);
        $docType = DocumentType::create(['name' => 'Informed Consent', 'code' => 'icf', 'is_required' => true, 'sort_order' => 1, 'is_active' => true]);
        $reqDoc = RequestDocument::create([
            'ethics_request_id' => $request->id,
            'document_type_id' => $docType->id,
            'status' => DocumentStatus::UnderReview,
        ]);

        // Should execute cleanly without attempting to send to empty/null email
        app(DocumentService::class)->requestRevision($reqDoc, $reviewer);

        Mail::assertNothingSent();
    }
}
