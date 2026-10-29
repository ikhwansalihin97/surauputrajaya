<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ApplicationDocument extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Medan yang dibenarkan untuk penetapan pukal (Mass Assignment).
     */
    protected $fillable = [
        'application_id',
        'jenis_dokumen',
        'fail_path',
        'fail_nama_asal',
    ];

    /**
     * Tetapan Automatik untuk Spatie Activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'jenis_dokumen',
                'fail_nama_asal',
                'fail_path',
            ]) // Medan dokumen yang dipantau
            ->logOnlyDirty() // Rakam hanya jika ada perubahan sahaja
            ->useLogName('dokumen_surau') // Kategori log khusus untuk dokumen
            ->setDescriptionForEvent(function(string $eventName) {
                return "Dokumen '{$this->jenis_dokumen}' ({$this->fail_nama_asal}) telah {$eventName}";
            });
    }

    /**
     * Hubungan (Relationship): Dokumen sokongan milik satu Permohonan.
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}