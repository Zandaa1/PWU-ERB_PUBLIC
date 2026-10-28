<?php

namespace App\Services;

use App\Contracts\DocumentStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LocalDocumentStorage implements DocumentStorage
{
    public function __construct(private ImageCompressionService $compressor) {}

    public function store(UploadedFile $file, int $requestId, int $typeId, int $version = 1): string
    {
        $directory = "documents/{$requestId}/{$typeId}/{$version}";
        $originalName = $file->getClientOriginalName();
        $ext = strtolower($file->getClientOriginalExtension() ?: 'pdf');

        // Images: compress to web-friendly before storing
        if ($this->compressor->isImage($file)) {
            $compressed = $this->compressor->compress($file);
            if ($compressed !== null) {
                $compressedExt = $this->compressor->compressedExtension($file);
                // Preserve base name but use compressed extension if converted (png -> webp)
                $base = pathinfo($originalName, PATHINFO_FILENAME);
                $filename = $base.'.'.$compressedExt;
                // If extension changed, also update stored filename for viewer mime detection
                $fullPath = $directory.'/'.uniqid('', true).'_'.$filename;
                Storage::disk('local')->put($fullPath, $compressed);

                return $fullPath;
            }
        }

        // PDFs and non-compressible or small images: store original
        return $file->store($directory, 'local');
    }
}
