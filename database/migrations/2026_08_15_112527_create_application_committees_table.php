<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_committees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->string('nama');
            $table->string('ic_no');
            $table->string('jawatan'); // Pengerusi, Setiausaha, Bendahari, AJK
            $table->text('alamat');
            $table->string('no_tel');
            $table->string('email');
            $table->boolean('is_applicant')->default(false); // Tandakan jika Pemohon Utama
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_committees');
    }
};