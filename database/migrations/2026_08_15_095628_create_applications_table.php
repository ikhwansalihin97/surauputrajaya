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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            
            // Maklumat Surau/Madrasah (Dokumen PDF)
            $table->string('nama_surau');
            $table->text('alamat_surau');
            $table->text('alamat_surat');
            $table->string('kariah_masjid');
            $table->string('no_tel_surau')->nullable();
            $table->string('kawasan_surau'); // Taman, Perkampungan, Flat, dll.
            $table->string('kawasan_lain')->nullable();
            $table->decimal('jarak_surau_berdekatan', 5, 2)->comment('dalam km');
            $table->string('nama_surau_berdekatan');
            $table->string('taraf_tapak'); // Diwakafkan, Dikhaskan, Kebenaran
            $table->string('no_lot_geran')->nullable();
            
            // Bangunan & Kewangan
            $table->string('jenis_bangunan'); // Kayu/Batu
            $table->string('tingkat_bangunan'); // Setingkat/Dua Tingkat
            $table->integer('tahun_didirikan');
            $table->decimal('jumlah_perbelanjaan', 12, 2);
            $table->text('maklumat_bank'); // Nama, Alamat Bank, No Akaun, Penama
            $table->string('tempoh_mesyuarat_agung');
            $table->date('tarikh_genap_tempoh');
            $table->integer('penduduk_lelaki');
            $table->integer('penduduk_perempuan');
            
            // Aktiviti Agama & Sumber Kewangan
            $table->integer('kelas_pengajian_sebulan')->default(0);
            $table->integer('ceramah_sebulan')->default(0);
            $table->string('kelas_didikan_kanak')->nullable();
            $table->json('sumber_kewangan'); // Array 5 sumber
            
            // Pemohon (Akan didaftarkan sebagai User Pentadbir Surau)
            $table->string('pemohon_nama');
            $table->string('pemohon_jawatan');
            $table->string('pemohon_ic')->unique();
            $table->string('pemohon_email')->unique();
            $table->string('pemohon_tel');
            
            // Status Permohonan
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
