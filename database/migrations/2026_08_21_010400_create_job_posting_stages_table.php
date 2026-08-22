<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_posting_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')
                ->constrained('job_postings')
                ->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('order');
            $table->boolean('is_final')->default(false);
            $table->timestamps();

            $table->unique(['job_posting_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_posting_stages');
    }
};