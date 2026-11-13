<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Jemputan ke Mesyuarat Agung Tahunan (nama & e-mel).
 */
class SurauAgmJemputan extends Model
{
    protected $table = 'surau_agm_jemputan';

    protected $fillable = ['agm_mesyuarat_id', 'nama', 'email', 'bilangan_hantar', 'dihantar_at', 'status_hantar', 'created_by'];

    protected $casts = [
        'dihantar_at' => 'datetime',
    ];

    public function mesyuarat()
    {
        return $this->belongsTo(SurauAgmMesyuarat::class, 'agm_mesyuarat_id');
    }
}
