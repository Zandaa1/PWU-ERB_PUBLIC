<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'Arts & Sciences',
            'Business Education',
            'Education',
            'Fine Arts & Design',
            'Food Science & Technology',
            'Hospitality Management',
            'International Relations & Diplomacy',
            'Medical Technology',
            'Music',
            'Nursing',
            'Nutrition',
            'Pharmacy',
            'Social Work',
            'Tourism',
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept], ['is_active' => true]);
        }
    }
}
