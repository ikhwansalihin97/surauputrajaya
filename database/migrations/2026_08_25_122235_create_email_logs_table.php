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
        Schema::create('email_logs', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | PERMOHONAN
            |--------------------------------------------------------------------------
            */

            $table->foreignId('application_id')
                ->nullable()
                ->constrained('applications')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | JENIS EMAIL
            |--------------------------------------------------------------------------
            |
            | Contoh:
            | PERMOHONAN_BAHARU
            | PENGESAHAN_MASJID
            | PENGESAHAN_ZON
            | PENGESAHAN_JAWI
            | EMAIL_PEMOHON
            |
            */

            $table->string('mail_type')->nullable();

            /*
            |--------------------------------------------------------------------------
            | PENERIMA
            |--------------------------------------------------------------------------
            */

            $table->text('to_email')->nullable();

            $table->text('cc_email')->nullable();

            $table->text('bcc_email')->nullable();

            /*
            |--------------------------------------------------------------------------
            | EMAIL
            |--------------------------------------------------------------------------
            */

            $table->string('subject')->nullable();

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            |
            | SENT
            | FAILED
            |
            */

            $table->string('status', 20)->default('SENT');

            /*
            |--------------------------------------------------------------------------
            | ERROR
            |--------------------------------------------------------------------------
            */

            $table->text('error_message')->nullable();

            /*
            |--------------------------------------------------------------------------
            | TARIKH
            |--------------------------------------------------------------------------
            */

            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index('mail_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};