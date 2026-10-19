<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('student')->after('email'); // student | staff | admin
            $table->string('student_id', 30)->nullable()->unique()->after('role');
            $table->string('course', 100)->nullable()->after('student_id');
            $table->boolean('is_active')->default(true)->after('course');
            $table->string('otp_code')->nullable();          // stored HASHED, never plain text
            $table->timestamp('otp_expires_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['student_id']);
            $table->dropColumn(['role', 'student_id', 'course', 'is_active', 'otp_code', 'otp_expires_at']);
        });
    }
};
