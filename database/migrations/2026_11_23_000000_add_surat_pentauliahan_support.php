<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Surat Pentauliahan AJK.
 *
 * - tetapan : tetapan sistem (kunci/nilai) cth. kepala surat JAWI, no. rujukan fail, salinan kepada
 * - users.tandatangan_path / blok_tandatangan : tandatangan pegawai untuk surat yang diluluskan
 * - surau_agm_mesyuarat.surat_* : no. rujukan, tarikh dan penandatangan surat pentauliahan
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tetapan', function (Blueprint $table) {
            $table->id();
            $table->string('kunci')->unique();
            $table->text('nilai')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('tandatangan_path')->nullable();
            $table->text('blok_tandatangan')->nullable();
        });

        Schema::table('surau_agm_mesyuarat', function (Blueprint $table) {
            $table->string('surat_no_rujukan')->nullable()->after('dihantar_pengesahan_at');
            $table->date('surat_tarikh')->nullable()->after('surat_no_rujukan');
            $table->foreignId('surat_ditandatangani_oleh')->nullable()->after('surat_tarikh')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('surau_agm_mesyuarat', function (Blueprint $table) {
            $table->dropConstrainedForeignId('surat_ditandatangani_oleh');
            $table->dropColumn(['surat_no_rujukan', 'surat_tarikh']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tandatangan_path', 'blok_tandatangan']);
        });
        Schema::dropIfExists('tetapan');
    }
};
