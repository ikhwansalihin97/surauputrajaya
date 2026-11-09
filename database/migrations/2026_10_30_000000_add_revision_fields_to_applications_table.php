<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sokongan permohonan kemaskini maklumat surau.
     *
     * - jenis     : BARU (pendaftaran asal) / KEMASKINI (semakan semula maklumat)
     * - parent_id : permohonan asal (BARU) bagi setiap permohonan KEMASKINI
     * - status    : tambah DRAFT untuk kemaskini yang belum dihantar
     *
     * Unique pada pemohon_ic & pemohon_email dibuang kerana setiap kemaskini
     * membawa maklumat pemohon yang sama dengan permohonan asal.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropUnique(['pemohon_ic']);
            $table->dropUnique(['pemohon_email']);

            $table->string('jenis')->default('BARU')->after('reference_no');
            $table->foreignId('parent_id')->nullable()->after('jenis')
                ->constrained('applications')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable()->after('status');

            $table->index('pemohon_ic');
            $table->index('pemohon_email');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->enum('status', ['DRAFT', 'PENDING', 'APPROVED', 'REJECTED'])
                ->default('PENDING')->change();
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])
                ->default('PENDING')->change();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
            $table->dropColumn(['jenis', 'submitted_at']);
            $table->dropIndex(['pemohon_ic']);
            $table->dropIndex(['pemohon_email']);
            $table->unique('pemohon_ic');
            $table->unique('pemohon_email');
        });
    }
};
