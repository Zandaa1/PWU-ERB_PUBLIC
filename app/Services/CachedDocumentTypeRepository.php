<?php

namespace App\Services;

use App\Contracts\DocumentTypeRepository;
use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CachedDocumentTypeRepository implements DocumentTypeRepository
{
    public function active(): Collection
    {
        return DocumentType::getActive();
    }

    public function find(int $id): ?DocumentType
    {
        return $this->active()->firstWhere('id', $id);
    }

    public static function bust(): void
    {
        Cache::forget('document_types:active');
        Cache::forget('document_types:required');
    }
}
