<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pipeline_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pipeline_template_id')
                ->constrained('pipeline_templates')
                ->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('order');
            $table->boolean('is_final')->default(false);
            $table->timestamps();

            $table->unique(['pipeline_template_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pipeline_stages');
    }
};