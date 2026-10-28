<?php

namespace Database\Seeders;

use App\Models\ControlNumberCounter;
use Illuminate\Database\Seeder;

class ControlNumberSeeder extends Seeder
{
    public function run(): void
    {
        $year = date('Y');
        ControlNumberCounter::firstOrCreate(
            ['scope' => "ERB{$year}"],
            ['last_sequence' => 0]
        );
    }
}
