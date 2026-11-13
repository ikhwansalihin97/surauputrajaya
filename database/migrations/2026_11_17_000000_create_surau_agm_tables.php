<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mesyuarat Agung Tahunan (AGM) surau.
 *
 * - surau_agm_sesi      : sesi dua tahun (cth. 2026/2027)
 * - surau_agm_mesyuarat : mesyuarat Bil. 1 (tahun mula) dan Bil. 2 (tahun tamat) dalam sesi
 * - surau_committees.agm_sesi_id : AJK disimpan mengikut sesi (sejarah dikekalkan)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surau_agm_sesi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surau_id')->constrained('suraus')->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun_mula');
            $table->unsignedSmallInteger('tahun_tamat');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['surau_id', 'tahun_mula']);
        });

        Schema::create('surau_agm_mesyuarat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agm_sesi_id')->constrained('surau_agm_sesi')->cascadeOnDelete();
            $table->unsignedTinyInteger('bilangan'); // 1 atau 2
            $table->unsignedSmallInteger('tahun');
            $table->date('tarikh');
            $table->time('masa');
            $table->string('lokasi');
            $table->string('minit_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['agm_sesi_id', 'bilangan']);
        });

        Schema::table('surau_committees', function (Blueprint $table) {
            $table->foreignId('agm_sesi_id')->nullable()->after('surau_id')
                ->constrained('surau_agm_sesi')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('surau_committees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('agm_sesi_id');
        });
        Schema::dropIfExists('surau_agm_mesyuarat');
        Schema::dropIfExists('surau_agm_sesi');
    }
};
