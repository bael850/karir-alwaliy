<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_stage_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')
                ->constrained('applications')
                ->cascadeOnDelete();
            $table->foreignId('from_stage_id')
                ->nullable()
                ->constrained('job_posting_stages')
                ->nullOnDelete();
            $table->foreignId('to_stage_id')
                ->constrained('job_posting_stages')
                ->cascadeOnDelete();
            $table->foreignId('moved_by')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->text('note')->nullable();
            $table->timestamp('moved_at')->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_stage_history');
    }
};