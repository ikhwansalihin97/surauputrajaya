<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Rekod rasmi surau.
 *
 * Diwujudkan apabila permohonan pendaftaran (BARU) diluluskan, dan selepas itu
 * dikemaskini terus oleh Pentadbir Surau tanpa menyentuh jadual applications.
 * Jadual applications kekal sebagai salinan borang yang dihantar.
 */
class Surau extends Model
{
    use LogsActivity;

    protected $table = 'suraus';

    /** Medan profil surau yang dikongsi dengan borang permohonan */
    public const PROFILE_FIELDS = [
        'nama_surau', 'kategori_surau', 'masjid_id', 'no_tel_surau', 'alamat_surau', 'alamat_surat',
        'kawasan_surau', 'kawasan_lain', 'jarak_surau_berdekatan', 'nama_surau_berdekatan', 'taraf_tapak',
        'no_lot_geran', 'jenis_bangunan', 'tingkat_bangunan', 'tahun_didirikan', 'jumlah_perbelanjaan',
        'nama_bank', 'no_akaun_bank', 'alamat_bank', 'penama_akaun', 'sumber_kewangan',
        'penduduk_lelaki', 'penduduk_perempuan', 'kelas_pengajian_sebulan', 'ceramah_sebulan', 'kelas_kanak_sebulan',
    ];

    protected $fillable = [
        ...self::PROFILE_FIELDS,
        'application_id',
        'user_id',
        'no_rujukan_jawi',
        'created_by',
    ];

    protected $casts = [
        'sumber_kewangan' => 'array',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function masjid()
    {
        return $this->belongsTo(Masjid::class);
    }

    /**
     * AJK semasa: AJK bagi sesi AGM terkini (atau AJK tanpa sesi jika tiada sesi lagi).
     */
    public function committees()
    {
        return $this->hasMany(SurauCommittee::class)
            ->whereRaw('surau_committees.agm_sesi_id <=> (
                select s.id from surau_agm_sesi s
                where s.surau_id = surau_committees.surau_id
                order by s.tahun_mula desc limit 1
            )')
            ->orderBy('id');
    }

    /** Semua AJK termasuk sesi terdahulu */
    public function semuaCommittees()
    {
        return $this->hasMany(SurauCommittee::class)->orderBy('id');
    }

    public function agmSesi()
    {
        return $this->hasMany(SurauAgmSesi::class)->orderByDesc('tahun_mula');
    }

    public function pertukaranPentadbir()
    {
        return $this->hasMany(SurauPertukaranPentadbir::class)->latest('id');
    }

    /** Permintaan serah tugas yang masih menunggu (paling banyak satu) */
    public function pertukaranMenunggu(): ?SurauPertukaranPentadbir
    {
        return $this->pertukaranPentadbir()->menunggu()->with('kepada')->first();
    }

    public function sesiSemasa(): ?SurauAgmSesi
    {
        return $this->agmSesi()->first();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Maklumat Pentadbir Surau dalam bentuk baris AJK (belum disimpan).
     * Jawatan diambil daripada akaun pengguna; jika bukan jawatan AJK yang sah, dianggap 'AJK'.
     */
    public function pentadbirSebagaiAjk(?string $jawatan = null): ?array
    {
        $user = $this->user;
        if (!$user) {
            return null;
        }

        $jawatan = $jawatan ?: $user->jawatan;

        return [
            'nama'    => $user->name,
            'ic_no'   => preg_replace('/\D+/', '', (string) $user->no_kad_pengenalan),
            'jawatan' => in_array($jawatan, SurauCommittee::JAWATAN, true) ? $jawatan : 'AJK',
            'alamat'  => null,
            'no_tel'  => preg_replace('/\D+/', '', (string) ($user->no_telefon_bimbit ?: $user->no_telefon_pejabat)),
            'email'   => $user->email,
        ];
    }

    /**
     * Senarai AJK untuk dipaparkan dalam borang. Jika Pentadbir Surau belum tersenarai
     * (cth. surau sedia ada yang didaftarkan oleh JAWI), maklumatnya dimasukkan secara automatik.
     */
    public function committeesForForm(): \Illuminate\Support\Collection
    {
        $rows = $this->committees->map->only(['nama', 'ic_no', 'jawatan', 'alamat', 'no_tel', 'email']);

        // Sesi baharu yang belum diisi: mulakan dengan AJK sesi terdahulu
        if ($rows->isEmpty()) {
            $semasa = $this->sesiSemasa();
            $terdahulu = $this->semuaCommittees()
                ->when($semasa, fn ($q) => $q->where(fn ($w) => $w->whereNull('agm_sesi_id')->orWhere('agm_sesi_id', '!=', $semasa->id)))
                ->get()
                ->groupBy(fn ($c) => (int) $c->agm_sesi_id)
                ->sortKeysDesc()
                ->first();
            if ($terdahulu) {
                $rows = $terdahulu->map->only(['nama', 'ic_no', 'jawatan', 'alamat', 'no_tel', 'email']);
            }
        }

        $pentadbir = $this->pentadbirSebagaiAjk();
        if ($pentadbir) {
            $ic = $pentadbir['ic_no'];
            $sudahAda = $rows->contains(fn ($c) => $ic !== '' && preg_replace('/\D+/', '', (string) $c['ic_no']) === $ic);
            if (!$sudahAda) {
                $rows->push($pentadbir);
            }
        }

        return $rows->values();
    }

    /**
     * Surau sedia ada: didaftarkan terus oleh Pentadbir JAWI, tanpa permohonan.
     */
    public function isSediaAda(): bool
    {
        return $this->application_id === null;
    }

    /**
     * Gantikan senarai AJK surau dengan AJK daripada permohonan yang diluluskan.
     */
    public function syncCommitteesFrom(Application $application): void
    {
        $this->committees()->delete();

        foreach ($application->committees()->orderBy('id')->get() as $c) {
            $this->committees()->create($c->only(['nama', 'ic_no', 'jawatan', 'alamat', 'no_tel', 'email']));
        }
    }

    /**
     * Medan profil daripada permohonan, dengan sumber_kewangan dinormalkan
     * (rekod lama mungkin menyimpannya sebagai JSON berganda).
     */
    public static function profileFrom(Application $application, ?array $only = null): array
    {
        $data = collect($application->only($only ?? self::PROFILE_FIELDS));

        if ($data->has('sumber_kewangan')) {
            $sumber = $data['sumber_kewangan'];
            while (is_string($sumber) && ($decoded = json_decode($sumber, true)) !== null) {
                $sumber = $decoded;
            }
            $data['sumber_kewangan'] = array_values(array_filter((array) $sumber));
        }

        return $data->all();
    }

    /**
     * Kemaskini rekod surau selepas permohonan diluluskan.
     *
     * - BARU      : wujudkan rekod surau daripada borang.
     * - KEMASKINI : hanya medan yang diubah dalam borang kemaskini (berbanding rekod
     *               lulus sebelumnya) disalin, supaya kemaskini terus oleh Pentadbir
     *               Surau tidak ditindih dengan nilai lama.
     */
    public static function syncFromApprovedApplication(Application $application, ?Application $previousApproved = null): self
    {
        $root = $application->isRevision() ? ($application->parent ?? $application) : $application;

        $surau = $application->surau
            ?? $root->surau
            ?? static::where('application_id', $root->id)->first();

        if (!$surau) {
            $surau = static::create(static::profileFrom($application) + [
                'application_id' => $root->id,
                'user_id'        => $application->user_id,
            ]);
            $surau->syncCommitteesFrom($application);
        } else {
            $changed = static::profileFrom($application);

            if ($previousApproved) {
                $before  = static::profileFrom($previousApproved);
                $changed = array_filter(
                    $changed,
                    fn ($value, $field) => $value != ($before[$field] ?? null),
                    ARRAY_FILTER_USE_BOTH
                );
            }

            $surau->fill($changed);
            $surau->user_id = $application->user_id ?? $surau->user_id;
            $surau->save();

            // Kemaskini yang diluluskan membawa senarai AJK yang disemak
            if ($application->isRevision()) {
                $surau->syncCommitteesFrom($application);
            }
        }

        Application::withDrafts()
            ->chainOf($root->id)
            ->whereNull('surau_id')
            ->update(['surau_id' => $surau->id]);

        return $surau;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([...self::PROFILE_FIELDS, 'user_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('surau')
            ->setDescriptionForEvent(
                fn (string $eventName) => "Maklumat surau '{$this->nama_surau}' telah {$eventName}"
            );
    }
}
