<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Rekod rasmi surau diasingkan daripada jadual permohonan.
 *
 * - suraus              : maklumat semasa surau (dikemaskini terus oleh Pentadbir Surau)
 * - applications.surau_id : pautan setiap permohonan (BARU / KEMASKINI) kepada suraunya
 *
 * Jadual applications kekal sebagai salinan borang yang dihantar
 * (rekod sejarah & semakan), dan tidak lagi menjadi rekod rasmi surau.
 */
return new class extends Migration
{
    /** Medan profil surau yang disalin daripada permohonan */
    private const FIELDS = [
        'nama_surau', 'kategori_surau', 'masjid_id', 'no_tel_surau', 'alamat_surau', 'alamat_surat',
        'kawasan_surau', 'kawasan_lain', 'jarak_surau_berdekatan', 'nama_surau_berdekatan', 'taraf_tapak',
        'no_lot_geran', 'jenis_bangunan', 'tingkat_bangunan', 'tahun_didirikan', 'jumlah_perbelanjaan',
        'nama_bank', 'no_akaun_bank', 'alamat_bank', 'penama_akaun', 'sumber_kewangan',
        'penduduk_lelaki', 'penduduk_perempuan', 'kelas_pengajian_sebulan', 'ceramah_sebulan', 'kelas_kanak_sebulan',
    ];

    public function up(): void
    {
        Schema::create('suraus', function (Blueprint $table) {
            $table->id();

            // Permohonan pendaftaran asal (BARU) yang mewujudkan surau ini
            $table->foreignId('application_id')->nullable()->unique()
                ->constrained('applications')->nullOnDelete();

            // Pentadbir Surau semasa
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->foreignId('masjid_id')->nullable()
                ->constrained('masjids')->nullOnDelete();

            $table->string('nama_surau');
            $table->string('kategori_surau');
            $table->string('no_tel_surau')->nullable();
            $table->text('alamat_surau');
            $table->text('alamat_surat')->nullable();

            $table->string('kawasan_surau')->nullable();
            $table->string('kawasan_lain')->nullable();
            $table->decimal('jarak_surau_berdekatan', 5, 2)->nullable()->comment('dalam km');
            $table->string('nama_surau_berdekatan')->nullable();
            $table->string('taraf_tapak')->nullable();
            $table->string('no_lot_geran')->nullable();
            $table->string('jenis_bangunan')->nullable();
            $table->string('tingkat_bangunan')->nullable();
            $table->integer('tahun_didirikan')->nullable();
            $table->decimal('jumlah_perbelanjaan', 12, 2)->nullable();

            $table->string('nama_bank')->nullable();
            $table->string('no_akaun_bank')->nullable();
            $table->text('alamat_bank')->nullable();
            $table->text('penama_akaun')->nullable();
            $table->json('sumber_kewangan')->nullable();

            $table->integer('penduduk_lelaki')->default(0);
            $table->integer('penduduk_perempuan')->default(0);
            $table->integer('kelas_pengajian_sebulan')->default(0);
            $table->integer('ceramah_sebulan')->default(0);
            $table->integer('kelas_kanak_sebulan')->default(0);

            $table->timestamps();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('surau_id')->nullable()->after('parent_id')
                ->constrained('suraus')->nullOnDelete();
        });

        $this->backfill();
    }

    /**
     * Wujudkan rekod surau bagi setiap pendaftaran yang telah diluluskan,
     * berdasarkan permohonan LULUS terkini dalam rantaian (BARU + KEMASKINI).
     */
    private function backfill(): void
    {
        $roots = DB::table('applications')
            ->where('jenis', 'BARU')
            ->where('status', 'APPROVED')
            ->orderBy('id')
            ->get(['id']);

        foreach ($roots as $root) {
            $latest = DB::table('applications')
                ->where(fn ($q) => $q->where('id', $root->id)->orWhere('parent_id', $root->id))
                ->where('status', 'APPROVED')
                ->orderByDesc('id')
                ->first();

            $data = [];
            foreach (self::FIELDS as $field) {
                $data[$field] = $latest->{$field} ?? null;
            }

            // Rekod lama mungkin menyimpan sumber_kewangan sebagai JSON berganda
            $sumber = $data['sumber_kewangan'];
            while (is_string($sumber) && ($decoded = json_decode($sumber, true)) !== null) {
                $sumber = $decoded;
            }
            $data['sumber_kewangan'] = json_encode(array_values(array_filter((array) $sumber)));

            foreach (['penduduk_lelaki', 'penduduk_perempuan', 'kelas_pengajian_sebulan', 'ceramah_sebulan', 'kelas_kanak_sebulan'] as $int) {
                $data[$int] = (int) ($data[$int] ?? 0);
            }

            $surauId = DB::table('suraus')->insertGetId($data + [
                'application_id' => $root->id,
                'user_id'        => $latest->user_id,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            DB::table('applications')
                ->where(fn ($q) => $q->where('id', $root->id)->orWhere('parent_id', $root->id))
                ->update(['surau_id' => $surauId]);
        }
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('surau_id');
        });

        Schema::dropIfExists('suraus');
    }
};
