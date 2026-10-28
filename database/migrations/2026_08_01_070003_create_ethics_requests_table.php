<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ethics_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->foreignId('department_id')->constrained('departments');
            $table->string('status')->default('submitted');
            $table->string('control_number')->nullable()->unique();
            $table->date('approval_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('certificate_path')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ethics_requests');
    }
};
