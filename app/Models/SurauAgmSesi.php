<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Sesi Mesyuarat Agung Tahunan (AGM) surau, meliputi dua tahun (cth. 2026/2027).
 */
class SurauAgmSesi extends Model
{
    protected $table = 'surau_agm_sesi';

    protected $fillable = ['surau_id', 'tahun_mula', 'tahun_tamat', 'created_by'];

    public function surau()
    {
        return $this->belongsTo(Surau::class);
    }

    public function mesyuarat()
    {
        return $this->hasMany(SurauAgmMesyuarat::class, 'agm_sesi_id')->orderBy('bilangan');
    }

    public function committees()
    {
        return $this->hasMany(SurauCommittee::class, 'agm_sesi_id')->orderBy('id');
    }

    /** Cth. "2026/2027" */
    public function getLabelAttribute(): string
    {
        return $this->tahun_mula . '/' . $this->tahun_tamat;
    }

    /** Tahun bagi mesyuarat Bil. 1 atau Bil. 2 */
    public function tahunBagi(int $bilangan): int
    {
        return $this->tahun_mula + $bilangan - 1;
    }
}
