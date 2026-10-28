<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CleanOrphanUploadsTest extends TestCase
{
    public function test_clean_orphan_uploads_command_purges_old_temporary_files(): void
    {
        $tmpDir = storage_path('app/private/livewire-tmp');
        if (! File::exists($tmpDir)) {
            File::makeDirectory($tmpDir, 0755, true);
        }

        $dummyFile = $tmpDir.'/dummy_old_upload.pdf';
        File::put($dummyFile, 'dummy content');
        touch($dummyFile, time() - 90000); // 25 hours ago

        $this->artisan('app:clean-orphan-uploads', ['--hours' => 24])
            ->assertExitCode(0);

        $this->assertFileDoesNotExist($dummyFile);
    }

    public function test_clean_orphan_uploads_command_gracefully_handles_unreadable_directories(): void
    {
        $tmpDir = storage_path('app/private/livewire-tmp');
        if (! File::exists($tmpDir)) {
            File::makeDirectory($tmpDir, 0755, true);
        }

        $restrictedSubdir = $tmpDir.'/unreadable_subdir';
        if (! File::exists($restrictedSubdir)) {
            File::makeDirectory($restrictedSubdir, 0755, true);
        }
        chmod($restrictedSubdir, 0000);

        try {
            $this->artisan('app:clean-orphan-uploads', ['--hours' => 24])
                ->assertExitCode(0);
        } finally {
            chmod($restrictedSubdir, 0755);
            File::deleteDirectory($restrictedSubdir);
        }
    }
}
