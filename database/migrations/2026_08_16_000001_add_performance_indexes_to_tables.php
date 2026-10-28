<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ethics_requests', function (Blueprint $table) {
            $table->index(['status', 'department_id']);
            $table->index(['student_id', 'created_at']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'is_read']);
        });

        Schema::table('request_documents', function (Blueprint $table) {
            $table->index(['ethics_request_id', 'status']);
        });

        Schema::table('status_logs', function (Blueprint $table) {
            $table->index('ethics_request_id');
        });
    }

    public function down(): void
    {
        Schema::table('ethics_requests', function (Blueprint $table) {
            $table->dropIndex(['status', 'department_id']);
            $table->dropIndex(['student_id', 'created_at']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_read']);
        });

        Schema::table('request_documents', function (Blueprint $table) {
            $table->dropIndex(['ethics_request_id', 'status']);
        });

        Schema::table('status_logs', function (Blueprint $table) {
            $table->dropIndex(['ethics_request_id']);
        });
    }
};
