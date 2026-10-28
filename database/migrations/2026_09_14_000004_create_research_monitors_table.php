<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('research_monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ethics_request_id')->unique()->constrained('ethics_requests')->cascadeOnDelete();
            $table->string('monitoring_status', 50)->default('active');
            $table->timestamp('approved_at');
            $table->timestamp('expires_at');
            $table->timestamp('last_report_submitted_at')->nullable();
            $table->timestamp('next_report_due_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('monitoring_status');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_monitors');
    }
};
