<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pengesahan JAWI bagi Keputusan Mesyuarat AGM (Langkah 5).
 *
 * - surau_agm_mesyuarat.pentadbir_pilihan / pentadbir_calon : pilihan Pentadbir Surau (Bil. 1) disimpan
 *   dahulu; serah tugas hanya dimulakan selepas JAWI mengesahkan.
 * - surau_agm_mesyuarat.status_pengesahan : null (belum dihantar), MENUNGGU, PERLU_PEMBETULAN, DISAHKAN
 * - surau_agm_pengesahan : satu baris bagi setiap pusingan penghantaran, dengan status & catatan setiap item.
 *   Item: minit, ajk, pentadbir (Bil. 1); minit, laporan_kewangan (Bil. 2).
 *   Status item: MENUNGGU, DILULUSKAN, DITOLAK (null = tidak berkaitan).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surau_agm_mesyuarat', function (Blueprint $table) {
            $table->string('pentadbir_pilihan', 10)->nullable()->after('laporan_kewangan_path'); // KEKAL, BAHARU
            $table->json('pentadbir_calon')->nullable()->after('pentadbir_pilihan');
            $table->string('status_pengesahan', 20)->nullable()->after('pentadbir_calon');
            $table->timestamp('dihantar_pengesahan_at')->nullable()->after('status_pengesahan');
            $table->index('status_pengesahan');
        });

        Schema::create('surau_agm_pengesahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agm_mesyuarat_id')->constrained('surau_agm_mesyuarat')->cascadeOnDelete();
            $table->unsignedSmallInteger('pusingan');

            foreach (['minit', 'laporan_kewangan', 'ajk', 'pentadbir'] as $item) {
                $table->string("{$item}_status", 20)->nullable();
                $table->text("{$item}_catatan")->nullable();
            }

            // Salinan pilihan pentadbir pada pusingan ini
            $table->string('pentadbir_pilihan', 10)->nullable();
            $table->json('pentadbir_calon')->nullable();

            $table->foreignId('dihantar_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dihantar_at')->nullable();
            $table->foreignId('disemak_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disemak_at')->nullable();
            $table->timestamps();

            $table->unique(['agm_mesyuarat_id', 'pusingan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surau_agm_pengesahan');
        Schema::table('surau_agm_mesyuarat', function (Blueprint $table) {
            $table->dropIndex(['status_pengesahan']);
            $table->dropColumn(['pentadbir_pilihan', 'pentadbir_calon', 'status_pengesahan', 'dihantar_pengesahan_at']);
        });
    }
};
