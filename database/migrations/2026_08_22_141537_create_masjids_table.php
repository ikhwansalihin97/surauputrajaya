<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('masjids', function (Blueprint $table) {
            $table->id();

            $table->string('nama_masjid');
            $table->text('alamat')->nullable();
            $table->string('no_tel', 50)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('masjids');
    }
};