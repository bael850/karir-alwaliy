<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')
                ->constrained('applicants')
                ->cascadeOnDelete();
            $table->foreignId('job_posting_id')
                ->constrained('job_postings')
                ->cascadeOnDelete();
            $table->foreignId('current_stage_id')
                ->nullable()
                ->constrained('job_posting_stages')
                ->nullOnDelete();

            $table->string('source')->nullable();
            $table->timestamp('applied_at')->useCurrent();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['applicant_id', 'job_posting_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};