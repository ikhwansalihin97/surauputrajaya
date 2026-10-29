<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MasjidUser extends Pivot
{
    use LogsActivity;

    protected $table = 'masjid_user';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('pengurusan_kariah')
            ->setDescriptionForEvent(
                function (string $eventName) {

                    $userName = $this->user?->name ?? 'Pengguna';

                    $masjidName = $this->masjid?->nama_masjid ?? 'Kariah/Masjid';

                    return match ($eventName) {

                        'created' =>
                            "{$userName} telah ditambah ke Kariah {$masjidName}.",

                        'deleted' =>
                            "{$userName} telah dikeluarkan daripada Kariah {$masjidName}.",

                        'updated' =>
                            "Hubungan {$userName} dengan Kariah {$masjidName} telah dikemaskini.",

                        default =>
                            "Hubungan {$userName} dengan Kariah {$masjidName} telah {$eventName}.",
                    };
                }
            );
    }

    /**
     * Pengguna
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /**
     * Masjid / Kariah
     */
    public function masjid(): BelongsTo
    {
        return $this->belongsTo(
            Masjid::class,
            'masjid_id'
        );
    }
}