<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Item yang ditolak JAWI dan telah dibetulkan oleh surau (belum dihantar semula).
 * Cth. ["minit", "ajk"]
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surau_agm_pengesahan', function (Blueprint $table) {
            $table->json('dibetulkan')->nullable()->after('pentadbir_calon');
        });
    }

    public function down(): void
    {
        Schema::table('surau_agm_pengesahan', function (Blueprint $table) {
            $table->dropColumn('dibetulkan');
        });
    }
};
