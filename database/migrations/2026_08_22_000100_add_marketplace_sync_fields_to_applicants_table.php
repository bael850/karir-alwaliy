<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            // ID pelamar di sisi marketplace — dipakai buat matching pas sync, biar idempotent (nggak dobel insert).
            $table->string('external_id')->nullable()->unique()->after('id');
            $table->timestamp('last_synced_at')->nullable()->after('retention_until');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn(['external_id', 'last_synced_at']);
        });
    }
};