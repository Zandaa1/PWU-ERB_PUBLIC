<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\DocumentType;
use App\Models\SystemAnnouncement;
use App\Models\User;
use App\Services\CachedDocumentTypeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LocalCachingTest extends TestCase
{
    use RefreshDatabase;

    public function test_departments_get_active_is_cached_and_auto_invalidates(): void
    {
        $initialCount = Department::active()->count();

        Department::create(['name' => 'College of Nursing Extra', 'is_active' => true]);
        Department::create(['name' => 'School of Education Extra', 'is_active' => true]);

        Cache::forget('departments:active');
        $this->assertFalse(Cache::has('departments:active'));

        $depts = Department::getActive();
        $this->assertCount($initialCount + 2, $depts);
        $this->assertTrue(Cache::has('departments:active'));

        // Saving a new department should automatically bust the cache
        Department::create(['name' => 'School of Pharmacy Extra', 'is_active' => true]);
        $this->assertFalse(Cache::has('departments:active'));

        $updatedDepts = Department::getActive();
        $this->assertCount($initialCount + 3, $updatedDepts);
        $this->assertTrue(Cache::has('departments:active'));
    }

    public function test_document_types_are_cached_and_auto_invalidates(): void
    {
        $initialCount = DocumentType::where('is_active', true)->count();

        Cache::forget('document_types:active');
        $repo = new CachedDocumentTypeRepository;

        $this->assertFalse(Cache::has('document_types:active'));
        $docs = $repo->active();
        $this->assertCount($initialCount, $docs);
        $this->assertTrue(Cache::has('document_types:active'));

        // Creating a document type should bust the cache
        $newDoc = DocumentType::create([
            'name' => 'Special Protocol Supplement',
            'is_required' => true,
            'is_active' => true,
            'sort_order' => 99,
        ]);

        $this->assertFalse(Cache::has('document_types:active'));
        $this->assertCount($initialCount + 1, $repo->active());

        // Updating a document type should bust the cache
        $newDoc->update(['name' => 'Updated Protocol Supplement']);
        $this->assertFalse(Cache::has('document_types:active'));
        $this->assertEquals('Updated Protocol Supplement', $repo->active()->firstWhere('id', $newDoc->id)->name);
    }

    public function test_system_announcement_is_cached_and_auto_invalidates(): void
    {
        $user = User::factory()->create();

        SystemAnnouncement::create([
            'type' => 'info',
            'title' => 'Scheduled Maintenance',
            'message' => 'The system will undergo maintenance tonight.',
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        Cache::forget('system_announcement:active');
        $this->assertFalse(Cache::has('system_announcement:active'));

        $active = SystemAnnouncement::getActive();
        $this->assertNotNull($active);
        $this->assertEquals('Scheduled Maintenance', $active->title);
        $this->assertTrue(Cache::has('system_announcement:active'));

        // Updating announcement status should bust cache
        $active->update(['is_active' => false]);
        $this->assertFalse(Cache::has('system_announcement:active'));
    }
}
