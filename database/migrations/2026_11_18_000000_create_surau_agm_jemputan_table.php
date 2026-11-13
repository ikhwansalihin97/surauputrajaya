<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Senarai jemputan Mesyuarat Agung Tahunan (dihantar melalui e-mel oleh sistem).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surau_agm_jemputan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agm_mesyuarat_id')->constrained('surau_agm_mesyuarat')->cascadeOnDelete();
            $table->string('nama');
            $table->string('email');
            $table->unsignedSmallInteger('bilangan_hantar')->default(0);
            $table->timestamp('dihantar_at')->nullable();
            $table->string('status_hantar', 20)->nullable(); // SENT / FAILED
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['agm_mesyuarat_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surau_agm_jemputan');
    }
};
