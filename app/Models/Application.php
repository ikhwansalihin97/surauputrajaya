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
    ];

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