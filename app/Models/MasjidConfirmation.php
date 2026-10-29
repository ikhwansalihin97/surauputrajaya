<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasjidConfirmation extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'masjid_id',
        'confirmed_by',
        'status',
        'catatan',
        'confirmed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    /**
     * Permohonan yang hendak disahkan
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Masjid yang mengesahkan
     */
    public function masjid()
    {
        return $this->belongsTo(Masjid::class);
    }

    /**
     * Pentadbir Masjid yang membuat pengesahan
     */
    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}