<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'tarikh_temujanji',
        'masa_temujanji',
        'tempat',
        'catatan',
        'ditetapkan_oleh',
    ];

    protected $casts = [
        'tarikh_temujanji' => 'date',
        'masa_temujanji' => 'datetime:H:i',
    ];

    /**
     * Permohonan
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(
            Application::class,
            'application_id'
        );
    }

    /**
     * Pegawai JAWI yang menetapkan temujanji
     */
    public function ditetapkanOleh(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'ditetapkan_oleh'
        );
    }
}