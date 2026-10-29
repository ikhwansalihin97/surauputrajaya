<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationConfirmation extends Model
{
    protected $fillable = [
        'application_id',
        'peringkat',
        'status',
        'confirmed_by',
        'catatan',
        'confirmed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
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
     * Pegawai yang membuat pengesahan
     */
    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'confirmed_by'
        );
    }
}