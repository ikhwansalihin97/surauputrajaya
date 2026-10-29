<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ApplicationMeetingMinutes extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'application_id',
        'tahun_minit',
        'fail_path',
        'status_pengesahan',
        'catatan_semakan',
        'tarikh_disahkan',
        'uploaded_by',
    ];

    // Formatkan tarikh_disahkan sebagai objek Carbon (Datetime)
    protected $casts = [
        'tarikh_disahkan' => 'datetime',
    ];

    /**
     * Tetapan Automatik untuk Spatie Activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'tahun_minit',
                'status_pengesahan',
                'catatan_semakan',
                'tarikh_disahkan',
                'fail_path',
            ]) // Medan minit mesyuarat yang dipantau
            ->logOnlyDirty() // Rakam hanya apabila ada perubahan data sahaja
            ->useLogName('minit_mesyuarat') // Kategori log khusus untuk minit mesyuarat
            ->setDescriptionForEvent(function(string $eventName) {
                return "Minit Mesyuarat Agung (AGM) Tahun {$this->tahun_minit} telah {$eventName}";
            });
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}