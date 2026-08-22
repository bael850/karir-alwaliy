<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // ID lamaran di sisi marketplace — dipakai buat matching pas sync.
            $table->string('external_id')->nullable()->unique()->after('id');
            $table->timestamp('last_synced_at')->nullable()->after('applied_at');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['external_id', 'last_synced_at']);
        });
    }
};