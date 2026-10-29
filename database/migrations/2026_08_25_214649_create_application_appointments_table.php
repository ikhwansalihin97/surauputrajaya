<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_appointments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('application_id')
                ->constrained('applications')
                ->cascadeOnDelete();

            $table->date('tarikh_temujanji');

            $table->time('masa_temujanji');

            $table->string('tempat')->nullable();

            $table->text('catatan')->nullable();

            $table->foreignId('ditetapkan_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique('application_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_appointments');
    }
};