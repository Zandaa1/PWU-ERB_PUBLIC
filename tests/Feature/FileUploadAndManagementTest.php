<?php

namespace Tests\Feature;

use App\Enums\RequestStatus;
use App\Livewire\CreateEthicsRequest;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\EthicsRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FileUploadAndManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;

    protected Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'committee', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->student = User::factory()->create([
            'email' => 'student@example.test',
            'is_active' => true,
        ]);
        $this->student->assignRole('student');

        $this->department = Department::create([
            'name' => 'School of Nursing',
            'code' => 'SN',
            'is_active' => true,
        ]);

        // Ensure standard document types exist
        $requiredTypes = DocumentType::where('is_required', true)->get();
        if ($requiredTypes->isEmpty()) {
            $types = [
                'Study Protocol',
                'Data Collection Forms (questionnaires) & CV of PI',
                'Official Receipt of ethics review fee',
                'Proof of Anti-Plagiarism Report and AI Content Report',
                'Certification of Approved Title and Compliance of Revisions after Proposal Defense',
                'Informed Consent Form (English)',
                'Informed Consent Form (Tagalog)',
            ];

            foreach ($types as $index => $typeName) {
                DocumentType::firstOrCreate(
                    ['name' => $typeName],
                    [
                        'is_required' => true,
                        'is_active' => true,
                        'sort_order' => $index + 1,
                    ]
                );
            }
        }
    }

    public function test_student_can_upload_and_set_document(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('protocol_v1.pdf', 1024, 'application/pdf');
        $docId = DocumentType::first()->id;

        Livewire::actingAs($this->student)
            ->test(CreateEthicsRequest::class)
            ->set('uploads.'.$docId, $file)
            ->assertSet('uploads.'.$docId, function ($val) {
                return $val && $val->getClientOriginalName() === 'protocol_v1.pdf';
            });
    }

    public function test_student_can_delete_and_clear_uploaded_document(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('protocol_v1.pdf', 1024, 'application/pdf');
        $docId = DocumentType::first()->id;

        Livewire::actingAs($this->student)
            ->test(CreateEthicsRequest::class)
            ->set('uploads.'.$docId, $file)
            ->assertSet('uploads.'.$docId, function ($val) {
                return $val !== null;
            })
            ->call('clearDocumentUpload', $docId)
            ->assertSet('uploads.'.$docId, null);
    }

    public function test_student_can_change_and_replace_uploaded_document(): void
    {
        Storage::fake('local');
        $initialFile = UploadedFile::fake()->create('initial_protocol.pdf', 1024, 'application/pdf');
        $replacementFile = UploadedFile::fake()->create('updated_protocol.pdf', 2048, 'application/pdf');
        $docId = DocumentType::first()->id;

        Livewire::actingAs($this->student)
            ->test(CreateEthicsRequest::class)
            ->set('uploads.'.$docId, $initialFile)
            ->assertSet('uploads.'.$docId, function ($val) {
                return $val && $val->getClientOriginalName() === 'initial_protocol.pdf';
            })
            ->set('uploads.'.$docId, $replacementFile)
            ->assertSet('uploads.'.$docId, function ($val) {
                return $val && $val->getClientOriginalName() === 'updated_protocol.pdf';
            });
    }

    public function test_student_can_submit_ethics_request_with_uploaded_files(): void
    {
        Storage::fake('local');

        $doc1 = UploadedFile::fake()->create('study_protocol.pdf', 1024, 'application/pdf');
        $doc2 = UploadedFile::fake()->create('consent_form.pdf', 1024, 'application/pdf');
        $doc3 = UploadedFile::fake()->create('instrument.pdf', 1024, 'application/pdf');
        $doc4 = UploadedFile::fake()->create('cv.pdf', 1024, 'application/pdf');
        $doc5 = UploadedFile::fake()->create('application.pdf', 1024, 'application/pdf');
        $doc6 = UploadedFile::fake()->create('proposal_approval.pdf', 1024, 'application/pdf');
        $doc7 = UploadedFile::fake()->create('fee_receipt.pdf', 1024, 'application/pdf');

        $requiredTypes = DocumentType::where('is_active', true)->where('is_required', true)->orderBy('sort_order')->get();
        $docs = [$doc1, $doc2, $doc3, $doc4, $doc5, $doc6, $doc7];
        $test = Livewire::actingAs($this->student)
            ->test(CreateEthicsRequest::class)
            ->set('title', 'Patient Care Ethics in Public Hospitals')
            ->set('co_authors', 'Jane Doe, John Smith')
            ->set('department_id', $this->department->id)
            ->set('research_domain', 'health');
        foreach ($requiredTypes as $idx => $type) {
            if (isset($docs[$idx])) {
                $test->set('uploads.'.$type->id, $docs[$idx]);
            }
        }
        $test->set('privacyConsentAccepted', true)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('showSuccessModal', true);

        $this->assertDatabaseHas('ethics_requests', [
            'student_id' => $this->student->id,
            'title' => 'Patient Care Ethics in Public Hospitals',
            'department_id' => $this->department->id,
            'status' => RequestStatus::Submitted->value,
        ]);

        $request = EthicsRequest::where('title', 'Patient Care Ethics in Public Hospitals')->first();
        $this->assertNotNull($request);
        $this->assertGreaterThanOrEqual(7, $request->requestDocuments->count());
        $this->assertDatabaseHas('status_logs', [
            'ethics_request_id' => $request->id,
            'new_status' => RequestStatus::Submitted->value,
        ]);
    }
}
