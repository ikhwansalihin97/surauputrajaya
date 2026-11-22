<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Satu pusingan penghantaran Keputusan Mesyuarat AGM untuk pengesahan JAWI.
 * Setiap item (minit, laporan_kewangan, ajk, pentadbir) mempunyai status dan catatan sendiri.
 */
class SurauAgmPengesahan extends Model
{
    protected $table = 'surau_agm_pengesahan';

    public const ITEM_LABEL = [
        'minit'            => 'Minit Mesyuarat',
        'laporan_kewangan' => 'Laporan Kewangan',
        'ajk'              => 'Senarai AJK',
        'pentadbir'        => 'Pentadbir Surau',
    ];

    protected $fillable = [
        'agm_mesyuarat_id', 'pusingan',
        'minit_status', 'minit_catatan',
        'laporan_kewangan_status', 'laporan_kewangan_catatan',
        'ajk_status', 'ajk_catatan',
        'pentadbir_status', 'pentadbir_catatan',
        'pentadbir_pilihan', 'pentadbir_calon', 'dibetulkan',
        'dihantar_oleh', 'dihantar_at', 'disemak_oleh', 'disemak_at',
    ];

    protected $casts = [
        'pentadbir_calon' => 'array',
        'dibetulkan'      => 'array',
        'dihantar_at'     => 'datetime',
        'disemak_at'      => 'datetime',
    ];

    public function mesyuarat()
    {
        return $this->belongsTo(SurauAgmMesyuarat::class, 'agm_mesyuarat_id');
    }

    public function penghantar()
    {
        return $this->belongsTo(User::class, 'dihantar_oleh');
    }

    public function penyemak()
    {
        return $this->belongsTo(User::class, 'disemak_oleh');
    }

    public function status(string $item): ?string
    {
        return $this->{"{$item}_status"};
    }

    /** Item ditolak yang telah dibetulkan oleh surau (belum dihantar semula) */
    public function telahDibetulkan(string $item): bool
    {
        return $this->status($item) === 'DITOLAK' && in_array($item, $this->dibetulkan ?? [], true);
    }

    public function catatan(string $item): ?string
    {
        return $this->{"{$item}_catatan"};
    }
}
