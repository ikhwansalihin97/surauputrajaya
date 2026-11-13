<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ahli Jawatankuasa (AJK) surau mengikut sesi AGM, dikemaskini terus oleh Pentadbir Surau.
 * agm_sesi_id null = AJK sebelum sesi AGM pertama direkodkan.
 */
class SurauCommittee extends Model
{
    protected $fillable = ['surau_id', 'agm_sesi_id', 'nama', 'ic_no', 'jawatan', 'alamat', 'no_tel', 'email'];

    public const JAWATAN = ['Pengerusi', 'Timbalan Pengerusi', 'Setiausaha', 'Bendahari', 'AJK'];

    /** Jawatan utama yang wajib diisi (seorang bagi setiap jawatan) */
    public const JAWATAN_WAJIB = ['Pengerusi', 'Timbalan Pengerusi', 'Setiausaha', 'Bendahari'];

    /** Bilangan maksimum Ahli Jawatankuasa biasa (selain jawatan utama) */
    public const MAKS_AJK = 8;

    protected static function booted(): void
    {
        // Baris baharu dimasukkan ke sesi AGM terkini surau jika sesi tidak dinyatakan
        static::creating(function (SurauCommittee $c) {
            if ($c->agm_sesi_id === null && $c->surau_id) {
                $c->agm_sesi_id = SurauAgmSesi::where('surau_id', $c->surau_id)->orderByDesc('tahun_mula')->value('id');
            }
        });
    }

    public function surau()
    {
        return $this->belongsTo(Surau::class);
    }

    public function sesi()
    {
        return $this->belongsTo(SurauAgmSesi::class, 'agm_sesi_id');
    }
}
