<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;

interface DocumentStorage
{
    /**
     * Store an uploaded file for a request/type/version.
     *
     * @return string stored relative path
     */
    public function store(UploadedFile $file, int $requestId, int $typeId, int $version = 1): string;
}
