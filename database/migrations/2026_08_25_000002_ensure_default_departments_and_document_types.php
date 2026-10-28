<?php

use App\Models\ControlNumberCounter;
use App\Models\Department;
use App\Models\DocumentType;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ensure baseline departments
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

        // 2. Ensure baseline required document types
        $types = [
            [
                'name' => 'Study Protocol',
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Data Collection Forms (questionnaires) & CV of PI',
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Official Receipt of ethics review fee',
                'is_required' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Proof of Anti-Plagiarism Report and AI Content Report',
                'is_required' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Certification of Approved Title and Compliance of Revisions after Proposal Defense',
                'is_required' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Informed Consent Form (English)',
                'is_required' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Informed Consent Form (Tagalog)',
                'is_required' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Additional / Supplementary Thesis File (optional)',
                'is_required' => false,
                'allow_multiple' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($types as $type) {
            DocumentType::updateOrCreate(
                ['name' => $type['name']],
                [
                    'is_required' => $type['is_required'],
                    'allow_multiple' => $type['allow_multiple'] ?? false,
                    'is_active' => true,
                    'sort_order' => $type['sort_order'],
                ]
            );
        }

        // 3. Ensure control number counter for current year
        $year = date('Y');
        ControlNumberCounter::firstOrCreate(
            ['scope' => "ERB{$year}"],
            ['last_sequence' => 0]
        );
    }

    public function down(): void
    {
        // Keep baseline data intact on rollback
    }
};
