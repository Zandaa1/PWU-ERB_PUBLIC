<?php

namespace App\Contracts;

use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Collection;

interface DocumentTypeRepository
{
    /** @return Collection<int, DocumentType> */
    public function active(): Collection;

    public function find(int $id): ?DocumentType;
}
