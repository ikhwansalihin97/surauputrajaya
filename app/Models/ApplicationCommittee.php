<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ApplicationCommittee extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Medan yang dibenarkan untuk penetapan pukal (Mass Assignment).
     */
    protected $fillable = [
        'application_id',
        'nama',
        'ic_no',
        'jawatan',
        'alamat',
        'no_tel',
        'email',
        'is_applicant',
    ];

    /**
     * Penukaran jenis data automatik (Casting).
     */
    protected $casts = [
        'is_applicant' => 'boolean',
    ];

    /**
     * Tetapan Automatik untuk Spatie Activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'nama',
                'ic_no',
                'jawatan',
                'no_tel',
                'email',
                'is_applicant',
            ]) // Medan AJK yang dipantau perubahan
            ->logOnlyDirty() // Rakam hanya apabila ada perubahan data sahaja
            ->useLogName('ajk_surau') // Kategori log khusus dalam pangkalan data
            ->setDescriptionForEvent(function(string $eventName) {
                return "Maklumat AJK '{$this->nama}' ({$this->jawatan}) telah {$eventName}";
            });
    }

    /**
     * Hubungan (Relationship): Ahli Jawatankuasa milik satu Permohonan.
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}