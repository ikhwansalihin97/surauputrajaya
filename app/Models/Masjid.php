<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Masjid extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_masjid',
        'alamat',
        'no_tel',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Pengguna / Pentadbir Masjid
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'masjid_user'
        )->withTimestamps();
    }

    /**
     * Permohonan surau dalam kariah masjid
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'masjid_id');
    }

    public function confirmations()
    {
        return $this->hasMany(MasjidConfirmation::class);
    }
}