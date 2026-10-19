<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();               // who submitted
            $table->foreignId('service_category_id')->constrained()->restrictOnDelete();  // which service
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); // staff handling it
            $table->string('subject', 120);
            $table->text('description');
            $table->string('priority', 20)->default('normal');   // low | normal | high | urgent
            $table->string('status', 20)->default('pending');    // pending | in_review | in_progress | resolved | rejected | cancelled
            $table->date('preferred_date')->nullable();
            $table->string('attachment_path')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();   // 1-5 satisfaction
            $table->text('feedback')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
