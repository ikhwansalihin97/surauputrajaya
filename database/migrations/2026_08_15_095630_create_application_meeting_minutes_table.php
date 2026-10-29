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
        Schema::create('application_meeting_minutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->integer('tahun_minit');
            $table->string('fail_path');
            $table->enum('status_pengesahan', ['DALAM_SEMAKAN', 'DISAHKAN', 'DITOLAK'])->default('DALAM_SEMAKAN');
            $table->text('catatan_semakan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_meeting_minutes');
    }
};
