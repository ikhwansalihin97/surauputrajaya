<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('application_appointments', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
            $table->dropUnique('application_appointments_application_id_unique');
            $table->foreign('application_id')
                ->references('id')
                ->on('applications')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('application_appointments', function (Blueprint $table) {
            $table->unique('application_id');
        });
    }
};
