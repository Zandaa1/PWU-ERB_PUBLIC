<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CleanOrphanUploads extends Command
{
    protected $signature = 'app:clean-orphan-uploads {--hours=24 : Delete temporary uploads older than given hours}';

    protected $description = 'Clean up abandoned Livewire temporary uploads to prevent storage bloat';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $cutoff = now()->subHours($hours)->timestamp;

        $tmpDirs = [
            storage_path('app/private/livewire-tmp'),
            storage_path('app/livewire-tmp'),
        ];

        $deletedFiles = 0;
        $freedBytes = 0;

        foreach ($tmpDirs as $dir) {
            if (! File::exists($dir) || ! is_readable($dir)) {
                continue;
            }

            try {
                $files = File::allFiles($dir);
            } catch (\Throwable $e) {
                $this->warn("Skipping unreadable directory [{$dir}]: {$e->getMessage()}");
                Log::warning("CleanOrphanUploads: unable to read temporary directory [{$dir}]", [
                    'exception' => $e->getMessage(),
                ]);
                continue;
            }

            foreach ($files as $file) {
                try {
                    if ($file->getMTime() < $cutoff) {
                        $size = $file->getSize();
                        if (File::delete($file->getPathname())) {
                            $freedBytes += $size;
                            $deletedFiles++;
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning("CleanOrphanUploads: unable to inspect or delete temporary file [{$file->getPathname()}]", [
                        'exception' => $e->getMessage(),
                    ]);
                }
            }
        }

        $mb = round($freedBytes / (1024 * 1024), 2);
        $this->info("Successfully purged {$deletedFiles} abandoned temporary uploads ({$mb} MB freed).");

        return Command::SUCCESS;
    }
}
