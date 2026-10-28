<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
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
                'template_file_path' => null,
            ],
            [
                'name' => 'Informed Consent Form (English)',
                'is_required' => true,
                'sort_order' => 6,
                'template_file_path' => null,
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
                    'template_file_path' => $type['template_file_path'] ?? null,
                ]
            );
        }
    }
}
