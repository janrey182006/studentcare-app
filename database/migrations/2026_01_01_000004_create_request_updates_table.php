<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->nullable();            // set when the entry records a status change
            $table->text('message');
            $table->boolean('is_internal')->default(false);       // staff-only note, hidden from students
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_updates');
    }
};
