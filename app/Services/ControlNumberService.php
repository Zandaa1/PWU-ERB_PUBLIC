<?php

namespace App\Services;

use App\Models\ControlNumberCounter;
use Illuminate\Support\Facades\DB;

/**
 * Service responsible for generating atomic, sequential control numbers for approved ethics requests.
 *
 * Format Specification:
 * - Format: `ERB[YEAR]_[SEQUENCE]` (e.g. `ERB2026_00001`)
 * - Annual Scoping: Each calendar year receives an isolated sequence starting at 00001.
 * - Concurrency Safety: Uses `lockForUpdate()` within a database transaction to prevent duplicate control numbers under high concurrent approvals.
 */
class ControlNumberService
{
    /**
     * Atomically generate the next sequential control number for the current calendar year.
     */
    public function generateNext(): string
    {
        return DB::transaction(function () {
            $year = date('Y');
            $scope = "ERB{$year}";

            $counter = ControlNumberCounter::lockForUpdate()
                ->firstOrCreate(
                    ['scope' => $scope],
                    ['last_sequence' => 0]
                );

            $counter->increment('last_sequence');
            $sequence = str_pad((string) $counter->last_sequence, 5, '0', STR_PAD_LEFT);

            return "{$scope}_{$sequence}";
        });
    }
}
