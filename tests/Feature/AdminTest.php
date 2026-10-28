<?php

namespace Tests\Feature;

use App\Livewire\Admin\DepartmentManagement;
use App\Livewire\Admin\DocumentTypeManagement;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Developer\DevPanel;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_panel()
    {
        $this->seed();

        $admin = User::where('email', 'dean@example.test')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Users & Roles');
    }

    public function test_non_admin_cannot_access_admin_panel()
    {
        $this->seed();

        $student = User::where('email', 'student@example.test')->first();
        $this->actingAs($student);

        $response = $this->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_departments_and_document_types_are_seeded()
    {
        $this->seed();

        $this->assertGreaterThanOrEqual(14, Department::count());
        $this->assertEquals(8, DocumentType::count());
    }

    public function test_developer_can_access_developer_panel()
    {
        $this->seed();
        $dev = User::where('email', 'dev@example.test')->first();

        $response = $this->actingAs($dev)->get('/admin/dev');
        $response->assertStatus(200);
        $response->assertSee('Developer Operational Maintenance Panel');
    }

    public function test_developer_can_stream_test_certificate()
    {
        $this->seed();
        $dev = User::where('email', 'dev@example.test')->first();

        $response = $this->actingAs($dev)->get('/admin/dev/test-certificate');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_developer_can_designate_and_clear_chairperson()
    {
        $this->seed();
        $dev = User::where('email', 'dev@example.test')->first();
        $committee = User::where('email', 'reviewer@example.test')->first();

        Livewire::actingAs($dev)
            ->test(DevPanel::class)
            ->call('designateChairperson', $committee->id)
            ->assertSee($committee->name);

        $this->assertTrue($committee->fresh()->is_chairperson);

        Livewire::actingAs($dev)
            ->test(DevPanel::class)
            ->call('removeChairperson')
            ->assertSee('No Chairperson Designated');

        $this->assertFalse($committee->fresh()->is_chairperson);
    }

    public function test_developer_account_is_hidden_from_admin_user_management()
    {
        $this->seed();
        $admin = User::where('email', 'dean@example.test')->first();
        $dev = User::where('email', 'dev@example.test')->first();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->assertDontSee($dev->email)
            ->assertDontSee('dev@example.test');
    }

    public function test_core_document_types_are_locked_and_custom_types_can_be_deleted()
    {
        $this->seed();
        $admin = User::where('email', 'dean@example.test')->first();
        $coreType = DocumentType::where('name', 'Study Protocol')->first();

        // 1. Attempt to delete core type -> rejected
        Livewire::actingAs($admin)
            ->test(DocumentTypeManagement::class)
            ->call('deleteDocumentType', $coreType->id)
            ->assertSee('permanently locked and cannot be deleted');

        $this->assertDatabaseHas('document_types', ['id' => $coreType->id]);

        // 2. Add custom document type
        Livewire::actingAs($admin)
            ->test(DocumentTypeManagement::class)
            ->set('newTypeName', 'Custom Supplemental Certificate')
            ->set('newTypeRequired', false)
            ->call('addDocumentType');

        $custom = DocumentType::where('name', 'Custom Supplemental Certificate')->first();
        $this->assertNotNull($custom);
        $this->assertFalse($custom->isCore());

        // 3. Delete custom document type -> success
        Livewire::actingAs($admin)
            ->test(DocumentTypeManagement::class)
            ->call('deleteDocumentType', $custom->id);

        $this->assertDatabaseMissing('document_types', ['id' => $custom->id]);
    }

    public function test_document_type_template_upload_and_validation()
    {
        $this->seed();
        $admin = User::where('email', 'dean@example.test')->first();
        $coreType = DocumentType::where('name', 'Study Protocol')->first();

        // 1. Upload without choosing a file -> validation error
        Livewire::actingAs($admin)
            ->test(DocumentTypeManagement::class)
            ->call('openTemplateModal', $coreType->id)
            ->set('modalMode', 'file')
            ->call('saveTemplateFile')
            ->assertHasErrors(['modalTemplateFile']);

        // 2. Upload valid PDF file -> success
        Storage::fake('public');
        $file = UploadedFile::fake()->create('protocol_template.pdf', 500, 'application/pdf');

        Livewire::actingAs($admin)
            ->test(DocumentTypeManagement::class)
            ->call('openTemplateModal', $coreType->id)
            ->set('modalMode', 'file')
            ->set('modalTemplateFile', $file)
            ->call('saveTemplateFile')
            ->assertHasNoErrors();

        $this->assertNotNull($coreType->fresh()->template_file_path);
    }

    public function test_core_departments_are_locked_and_custom_departments_can_be_deleted()
    {
        $this->seed();
        $admin = User::where('email', 'dean@example.test')->first();
        $coreDept = Department::where('name', 'Nursing')->first();

        // 1. Attempt to delete core department -> rejected
        Livewire::actingAs($admin)
            ->test(DepartmentManagement::class)
            ->call('deleteDepartment', $coreDept->id)
            ->assertSee('permanently locked and cannot be deleted');

        $this->assertDatabaseHas('departments', ['id' => $coreDept->id]);

        // 2. Add custom department
        Livewire::actingAs($admin)
            ->test(DepartmentManagement::class)
            ->set('newDepartmentName', 'Institute of Advanced Studies')
            ->call('addDepartment');

        $custom = Department::where('name', 'Institute of Advanced Studies')->first();
        $this->assertNotNull($custom);
        $this->assertFalse($custom->isCore());

        // 3. Delete custom department -> success
        Livewire::actingAs($admin)
            ->test(DepartmentManagement::class)
            ->call('deleteDepartment', $custom->id);

        $this->assertDatabaseMissing('departments', ['id' => $custom->id]);
    }

    public function test_developer_can_provision_users_and_promote_developers_in_dev_panel()
    {
        $this->seed();
        $dev = User::where('email', 'dev@example.test')->first();

        // 1. Pre-provision a new user with role via DevPanel UI
        Livewire::actingAs($dev)
            ->test(DevPanel::class)
            ->set('activeTab', 'users')
            ->set('newUserEmail', 'chancellor@example.test')
            ->set('newUserName', 'Demo Administrator')
            ->set('newUserRole', 'admin')
            ->set('newUserIsDeveloper', true)
            ->call('provisionUser')
            ->assertSee('chancellor@example.test');

        $chancellor = User::where('email', 'chancellor@example.test')->first();
        $this->assertNotNull($chancellor);
        $this->assertTrue($chancellor->is_developer);
        $this->assertTrue($chancellor->hasRole('admin'));

        // 2. Toggle developer status off
        Livewire::actingAs($dev)
            ->test(DevPanel::class)
            ->call('toggleDeveloper', $chancellor->id);

        $this->assertFalse($chancellor->fresh()->is_developer);
    }

    public function test_admin_can_update_document_type_google_doc_url_and_clear_it()
    {
        $this->seed();
        $admin = User::where('email', 'dean@example.test')->first();
        $docType = DocumentType::where('name', 'Informed Consent Form (Tagalog)')->first();

        // Set Google Docs link
        Livewire::actingAs($admin)
            ->test(DocumentTypeManagement::class)
            ->call('openTemplateModal', $docType->id)
            ->set('modalMode', 'google_doc')
            ->set('modalTemplateUrl', 'https://example.invalid/template-not-configured')
            ->call('saveGoogleDocLink');

        $this->assertEquals('https://example.invalid/template-not-configured', $docType->fresh()->template_file_path);
        $this->assertTrue($docType->fresh()->is_google_doc);

        // Clear template
        Livewire::actingAs($admin)
            ->test(DocumentTypeManagement::class)
            ->call('openTemplateModal', $docType->id)
            ->call('removeTemplate');

        $this->assertNull($docType->fresh()->template_file_path);
    }

    public function test_faq_page_dynamically_renders_document_types_and_google_doc_templates()
    {
        $this->seed();
        $docType = DocumentType::where('name', 'Certification of Approved Title and Compliance of Revisions after Proposal Defense')->first();
        $docType->update(['template_file_path' => 'https://example.invalid/template-not-configured']);

        $response = $this->get('/faq');
        $response->assertStatus(200);
        $response->assertSee('Certification of Approved Title and Compliance of Revisions after Proposal Defense');
        $response->assertSee('https://example.invalid/template-not-configured');
    }
}
