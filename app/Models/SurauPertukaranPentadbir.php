<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Permintaan serah tugas Pentadbir Surau.
 * Pentadbir semasa kekal sehingga calon menerima (DITERIMA); hanya satu permintaan MENUNGGU bagi setiap surau.
 */
class SurauPertukaranPentadbir extends Model
{
    protected $table = 'surau_pertukaran_pentadbir';

    public const TEMPOH_HARI = 14;

    protected $fillable = [
        'surau_id', 'agm_mesyuarat_id', 'dari_user_id', 'kepada_user_id',
        'akaun_baharu', 'calon_nama', 'calon_ic', 'calon_email', 'calon_no_tel', 'calon_jawatan',
        'status', 'tamat_at', 'diputuskan_at', 'diminta_oleh',
    ];

    protected $casts = [
        'akaun_baharu'  => 'boolean',
        'tamat_at'      => 'datetime',
        'diputuskan_at' => 'datetime',
    ];

    public function surau()
    {
        return $this->belongsTo(Surau::class);
    }

    public function mesyuarat()
    {
        return $this->belongsTo(SurauAgmMesyuarat::class, 'agm_mesyuarat_id');
    }

    public function dari()
    {
        return $this->belongsTo(User::class, 'dari_user_id');
    }

    public function kepada()
    {
        return $this->belongsTo(User::class, 'kepada_user_id');
    }

    public function scopeMenunggu($query)
    {
        return $query->where('status', 'MENUNGGU');
    }

    /** Permintaan menunggu yang belum tamat tempoh */
    public function scopeAktif($query)
    {
        return $query->where('status', 'MENUNGGU')
            ->where(fn ($q) => $q->whereNull('tamat_at')->orWhere('tamat_at', '>', now()));
    }

    public function telahTamat(): bool
    {
        return $this->tamat_at !== null && $this->tamat_at->isPast();
    }

    /** Akaun calon sudah diaktifkan (boleh log masuk) */
    public function akaunDiaktifkan(): bool
    {
        return $this->kepada !== null && empty($this->kepada->activation_token);
    }
}
