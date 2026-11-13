<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sokongan surau sedia ada (didaftarkan terus oleh Pentadbir JAWI) dan
 * senarai AJK pada rekod surau.
 *
 * - suraus.no_rujukan_jawi : nombor rujukan JAWI bagi surau sedia ada
 * - suraus.created_by      : pegawai yang mendaftarkan surau sedia ada
 * - suraus.alamat_surau    : boleh kosong (dilengkapkan kemudian oleh Pentadbir Surau)
 * - surau_committees       : senarai AJK semasa surau (dikemaskini terus oleh Pentadbir Surau)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suraus', function (Blueprint $table) {
            $table->string('no_rujukan_jawi')->nullable()->unique()->after('id');
            $table->foreignId('created_by')->nullable()->after('user_id')
                ->constrained('users')->nullOnDelete();
            $table->text('alamat_surau')->nullable()->change();
        });

        Schema::create('surau_committees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surau_id')->constrained('suraus')->cascadeOnDelete();
            $table->string('nama');
            $table->string('ic_no', 20);
            $table->string('jawatan'); // Pengerusi, Setiausaha, Bendahari, AJK
            $table->text('alamat')->nullable();
            $table->string('no_tel', 30)->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        // Salin AJK daripada permohonan LULUS terkini bagi setiap surau sedia ada
        foreach (DB::table('suraus')->whereNotNull('application_id')->get(['id', 'application_id']) as $surau) {
            $latest = DB::table('applications')
                ->where(fn ($q) => $q->where('id', $surau->application_id)->orWhere('parent_id', $surau->application_id))
                ->where('status', 'APPROVED')
                ->orderByDesc('id')
                ->value('id');

            if (!$latest) {
                continue;
            }

            $rows = DB::table('application_committees')
                ->where('application_id', $latest)
                ->orderBy('id')
                ->get()
                ->map(fn ($c) => [
                    'surau_id'   => $surau->id,
                    'nama'       => $c->nama,
                    'ic_no'      => $c->ic_no,
                    'jawatan'    => $c->jawatan,
                    'alamat'     => $c->alamat,
                    'no_tel'     => $c->no_tel,
                    'email'      => $c->email,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
                ->all();

            if ($rows) {
                DB::table('surau_committees')->insert($rows);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('surau_committees');

        Schema::table('suraus', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
            $table->dropUnique(['no_rujukan_jawi']);
            $table->dropColumn('no_rujukan_jawi');
        });
    }
};
