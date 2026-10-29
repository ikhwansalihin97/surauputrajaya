<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('application_meeting_minutes', function (Blueprint $table) {
            // Tambah kolum tarikh_disahkan & catatan_semakan (jika belum ada)
            $table->timestamp('tarikh_disahkan')->nullable()->after('status_pengesahan');
        });
    }

    public function down(): void
    {
        Schema::table('application_meeting_minutes', function (Blueprint $table) {
            $table->dropColumn(['tarikh_disahkan']);
        });
    }
};