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
        Schema::create('application_confirmations', function (Blueprint $table) {

            $table->id();

            // Permohonan yang disemak
            $table->foreignId('application_id')
                ->constrained('applications')
                ->cascadeOnDelete();

            // Peringkat semakan
            $table->enum('peringkat', [
                'MASJID',
                'ZON',
                'JAWI',
            ]);

            // Keputusan semakan
            $table->enum('status', [
                'PENDING',
                'APPROVED',
                'REJECTED',
            ])->default('PENDING');

            // Pegawai yang membuat semakan
            $table->foreignId('confirmed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Catatan / ulasan
            $table->text('catatan')->nullable();

            // Tarikh semakan
            $table->timestamp('confirmed_at')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Satu rekod aktif bagi setiap peringkat
            |--------------------------------------------------------------------------
            |
            | Contoh:
            |
            | Application 6 + MASJID
            | Application 6 + ZON
            | Application 6 + JAWI
            |
            */

            $table->unique([
                'application_id',
                'peringkat',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_confirmations');
    }
};