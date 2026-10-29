<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // 1. Tambah column baru
            $table->string('kategori_surau')->after('reference_no');
            $table->integer('kelas_kanak_sebulan')->default(0)->after('ceramah_sebulan');
            
            // 2. Pemisahan Maklumat Bank (Tambah column bank baru)
            $table->string('nama_bank')->nullable()->after('jumlah_perbelanjaan');
            $table->string('no_akaun_bank')->nullable()->after('nama_bank');
            $table->text('alamat_bank')->nullable()->after('no_akaun_bank');
            $table->text('penama_akaun')->nullable()->after('alamat_bank');

            // 3. Buang column lama yang tidak digunakan lagi
            $table->dropColumn([
                'maklumat_bank',
                'tempoh_mesyuarat_agung',
                'tarikh_genap_tempoh',
                'kelas_didikan_kanak'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Revert balik jika rollback
            $table->dropColumn([
                'kategori_surau',
                'kelas_kanak_sebulan',
                'nama_bank',
                'no_akaun_bank',
                'alamat_bank',
                'penama_akaun'
            ]);

            $table->text('maklumat_bank')->nullable();
            $table->string('tempoh_mesyuarat_agung')->nullable();
            $table->date('tarikh_genap_tempoh')->nullable();
            $table->string('kelas_didikan_kanak')->nullable();
        });
    }
};