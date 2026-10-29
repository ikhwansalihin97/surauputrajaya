<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class RoleUser extends Pivot
{
    use LogsActivity;

    protected $table = 'role_user';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('pengurusan_peranan')
            ->setDescriptionForEvent(
                function (string $eventName) {

                    $userName =
                        $this->user?->name ?? 'Pengguna';

                    $roleName =
                        $this->role?->name ?? 'Peranan';

                    return match ($eventName) {

                        'created' =>
                            "{$userName} telah diberikan peranan {$roleName}.",

                        'deleted' =>
                            "{$userName} telah dibuang daripada peranan {$roleName}.",

                        'updated' =>
                            "Peranan {$roleName} bagi {$userName} telah dikemaskini.",

                        default =>
                            "Peranan {$roleName} bagi {$userName} telah {$eventName}.",
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
     * Role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(
            Role::class,
            'role_id'
        );
    }
}