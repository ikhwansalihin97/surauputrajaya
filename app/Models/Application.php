<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\ApplicationConfirmation;

class Application extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'sumber_kewangan' => 'array',
        'submitted_at'    => 'datetime',
    ];

    /**
     * Draf kemaskini hanya dilihat oleh pentadbir surau yang menyediakannya.
     * Semua senarai, dashboard & semakan status lain tidak memaparkan DRAFT.
     * Guna Application::withDrafts() untuk menyertakannya.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('excludeDrafts', function ($query) {
            $query->where($query->getModel()->getTable() . '.status', '!=', 'DRAFT');
        });
    }

    public static function withDrafts()
    {
        return static::withoutGlobalScope('excludeDrafts');
    }

    /**
     * ============================================================
     * KEMASKINI MAKLUMAT SURAU (REVISI)
     * ============================================================
     * Permohonan BARU ialah rekod asal. Setiap kemaskini ialah permohonan
     * KEMASKINI baharu dengan parent_id = id permohonan asal, dan melalui
     * proses pengesahan yang sama. Rekod yang telah diluluskan tidak diubah.
     */
    public function isRevision(): bool
    {
        return $this->jenis === 'KEMASKINI';
    }

    public function rootId(): int
    {
        return $this->parent_id ?? $this->id;
    }

    public function parent()
    {
        return $this->belongsTo(Application::class, 'parent_id');
    }

    /**
     * Semua permohonan dalam rantaian surau yang sama (asal + kemaskini), termasuk draf.
     */
    public function scopeChainOf($query, int $rootId)
    {
        return $query->where(function ($q) use ($rootId) {
            $q->where('applications.id', $rootId)->orWhere('applications.parent_id', $rootId);
        });
    }

    /**
     * Rekod rasmi semasa surau: permohonan LULUS terkini dalam rantaian.
     * Hanya satu kemaskini boleh dibuka pada satu masa, jadi id terbesar = kelulusan terkini.
     */
    public function currentApproved(): ?Application
    {
        return static::chainOf($this->rootId())
            ->where('status', 'APPROVED')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Kemaskini yang masih dibuka (DRAF / DALAM PROSES) bagi surau ini.
     */
    public function openRevision(): ?Application
    {
        return static::withDrafts()
            ->chainOf($this->rootId())
            ->where('jenis', 'KEMASKINI')
            ->whereIn('status', ['DRAFT', 'PENDING'])
            ->orderByDesc('id')
            ->first();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'reference_no',
                'nama_surau',
                'kategori_surau',
                'kariah_masjid',
                'status',
                'pemohon_nama',
                'pemohon_email',
            ])
            ->logOnlyDirty()
            ->useLogName('permohonan')
            ->setDescriptionForEvent(
                fn(string $eventName) =>
                    "Permohonan surau '{$this->nama_surau}' telah {$eventName}"
            );
    }

    public function committees()
    {
        return $this->hasMany(ApplicationCommittee::class);
    }

    public function documents()
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function meetingMinutes()
    {
        return $this->hasMany(ApplicationMeetingMinutes::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Masjid tempat surau berada dalam kariah.
     */
    public function masjid()
    {
        return $this->belongsTo(Masjid::class);
    }

    /**
     * Semua rekod pengesahan permohonan.
     */
    public function confirmations()
    {
        return $this->hasMany(
            ApplicationConfirmation::class,
            'application_id'
        );
    }

    /**
     * Rekod penghantaran e-mel.
     */
    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }

    /**
     * Pengesahan peringkat MASJID.
     */
    public function masjidConfirmation()
    {
        return $this->hasOne(ApplicationConfirmation::class)
            ->where('peringkat', 'MASJID');
    }

    /**
     * Pengesahan peringkat ZON.
     */
    public function zonConfirmation()
    {
        return $this->hasOne(ApplicationConfirmation::class)
            ->where('peringkat', 'ZON');
    }

    /**
     * Pengesahan peringkat JAWI.
     */
    public function jawiConfirmation()
    {
        return $this->hasOne(ApplicationConfirmation::class)
            ->where('peringkat', 'JAWI');
    }

    public function appointment()
    {
        return $this->hasOne(
            ApplicationAppointment::class,
            'application_id'
        )->latestOfMany();
    }

    public function appointments()
    {
        return $this->hasMany(
            ApplicationAppointment::class,
            'application_id'
        )->latest();
    }

}