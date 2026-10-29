<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('no_kad_pengenalan', 20)
                ->nullable()
                ->unique()
                ->after('name');

            $table->string('jawatan', 150)
                ->nullable()
                ->after('no_kad_pengenalan');

            $table->string('gred', 50)
                ->nullable()
                ->after('jawatan');

            $table->string('skim_perkhidmatan', 150)
                ->nullable()
                ->after('gred');

            $table->enum('status_perkhidmatan', [
                'TETAP',
                'KONTRAK',
                'SAMBILAN',
                'LAIN_LAIN',
            ])
                ->nullable()
                ->after('skim_perkhidmatan');

            $table->string('no_telefon_pejabat', 30)
                ->nullable()
                ->after('status_perkhidmatan');

            $table->string('sambungan', 10)
                ->nullable()
                ->after('no_telefon_pejabat');

            $table->string('no_telefon_bimbit', 30)
                ->nullable()
                ->after('sambungan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropUnique([
                'no_kad_pengenalan'
            ]);

            $table->dropColumn([
                'no_kad_pengenalan',
                'jawatan',
                'gred',
                'skim_perkhidmatan',
                'status_perkhidmatan',
                'no_telefon_pejabat',
                'sambungan',
                'no_telefon_bimbit',
            ]);
        });
    }
};