<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Kehadiran ke Mesyuarat Agung Tahunan (nama & e-mel).
 */
class SurauAgmKehadiran extends Model
{
    protected $table = 'surau_agm_kehadiran';

    protected $fillable = ['agm_mesyuarat_id', 'agm_jemputan_id', 'nama', 'email', 'created_by'];

    public function mesyuarat()
    {
        return $this->belongsTo(SurauAgmMesyuarat::class, 'agm_mesyuarat_id');
    }

    public function jemputan()
    {
        return $this->belongsTo(SurauAgmJemputan::class, 'agm_jemputan_id');
    }
}
