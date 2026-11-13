<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rekod kehadiran Mesyuarat Agung Tahunan.
 * agm_jemputan_id dipautkan jika kehadiran ditambah daripada senarai jemputan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surau_agm_kehadiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agm_mesyuarat_id')->constrained('surau_agm_mesyuarat')->cascadeOnDelete();
            $table->foreignId('agm_jemputan_id')->nullable()->constrained('surau_agm_jemputan')->nullOnDelete();
            $table->string('nama');
            $table->string('email')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['agm_mesyuarat_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surau_agm_kehadiran');
    }
};
