<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Keputusan Mesyuarat AGM dan pertukaran Pentadbir Surau.
 *
 * - surau_agm_mesyuarat.laporan_kewangan_path : laporan kewangan (Bil. 2)
 * - surau_pertukaran_pentadbir : permintaan serah tugas Pentadbir Surau. Pentadbir semasa
 *   kekal sehingga calon log masuk dan menerima serah tugas. Calon dipadankan melalui e-mel;
 *   jika belum berdaftar, akaun dicipta semasa permintaan dibuat (akaun_baharu) dan perlu diaktifkan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surau_agm_mesyuarat', function (Blueprint $table) {
            $table->string('laporan_kewangan_path')->nullable()->after('minit_path');
        });

        Schema::create('surau_pertukaran_pentadbir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surau_id')->constrained('suraus')->cascadeOnDelete();
            $table->foreignId('agm_mesyuarat_id')->nullable()->constrained('surau_agm_mesyuarat')->nullOnDelete();
            $table->foreignId('dari_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Akaun calon (dipadankan melalui e-mel, atau dicipta semasa permintaan dibuat)
            $table->foreignId('kepada_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('akaun_baharu')->default(false);
            $table->string('calon_nama');
            $table->string('calon_ic', 20)->nullable();
            $table->string('calon_email');
            $table->string('calon_no_tel', 30)->nullable();
            $table->string('calon_jawatan')->nullable();

            $table->string('status', 20)->default('MENUNGGU'); // MENUNGGU, DITERIMA, DITOLAK, DIBATALKAN
            $table->timestamp('tamat_at')->nullable();
            $table->timestamp('diputuskan_at')->nullable();
            $table->foreignId('diminta_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['surau_id', 'status']);
            $table->index(['kepada_user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surau_pertukaran_pentadbir');
        Schema::table('surau_agm_mesyuarat', function (Blueprint $table) {
            $table->dropColumn('laporan_kewangan_path');
        });
    }
};
