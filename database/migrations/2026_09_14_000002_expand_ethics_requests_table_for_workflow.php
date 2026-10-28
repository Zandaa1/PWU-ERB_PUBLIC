<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ethics_requests', function (Blueprint $table) {
            $table->string('research_domain', 50)->nullable()->after('department_id');
            $table->boolean('is_local')->default(true)->after('research_domain');
            $table->string('host_country', 150)->nullable()->after('is_local');
            $table->string('review_type', 50)->nullable()->after('host_country');
            $table->decimal('plagiarism_similarity_score', 5, 2)->nullable()->after('review_type');
            $table->decimal('ai_similarity_score', 5, 2)->nullable()->after('plagiarism_similarity_score');
            $table->timestamp('plagiarism_verified_at')->nullable()->after('ai_similarity_score');
            $table->foreignId('screened_by')->nullable()->after('plagiarism_verified_at')->constrained('users')->nullOnDelete();
            $table->text('screening_notes')->nullable()->after('screened_by');
            $table->text('deliberation_notes')->nullable()->after('screening_notes');
            $table->string('host_country_cert_path', 255)->nullable()->after('certificate_path');
        });
    }

    public function down(): void
    {
        Schema::table('ethics_requests', function (Blueprint $table) {
            $table->dropForeign(['screened_by']);
            $table->dropColumn([
                'research_domain',
                'is_local',
                'host_country',
                'review_type',
                'plagiarism_similarity_score',
                'ai_similarity_score',
                'plagiarism_verified_at',
                'screened_by',
                'screening_notes',
                'deliberation_notes',
                'host_country_cert_path',
            ]);
        });
    }
};
