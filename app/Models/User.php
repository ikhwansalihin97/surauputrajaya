<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use Notifiable, LogsActivity;

    protected $fillable = [
        'name',
        'no_kad_pengenalan',
        'jawatan',
        'gred',
        'skim_perkhidmatan',
        'status_perkhidmatan',
        'no_telefon_pejabat',
        'sambungan',
        'no_telefon_bimbit',
        'agency_id',
        'department_id',
        'email',
        'password',
        'google2fa_secret',
        'google2fa_enabled',
        'is_active',
        'last_login_at',
        'is_first_login',
        'activation_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    protected $casts = [
        'password' => 'hashed',
        'is_active' => 'boolean',
        'google2fa_enabled' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * ============================================================
     * SPATIE ACTIVITY LOG
     * ============================================================
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'no_kad_pengenalan',
                'jawatan',
                'gred',
                'skim_perkhidmatan',
                'status_perkhidmatan',
                'no_telefon_pejabat',
                'sambungan',
                'no_telefon_bimbit',
                'agency_id',
                'department_id',
                'email',
                'is_active',
                'google2fa_enabled',
            ])
            ->logOnlyDirty()
            ->useLogName('pengurusan_pengguna')
            ->setDescriptionForEvent(
                fn (string $eventName) =>
                    "Akaun pengguna '{$this->name}' ({$this->email}) telah {$eventName}"
            );
    }

    /**
     * ============================================================
     * AGENSI
     * ============================================================
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(
            Agency::class,
            'agency_id'
        );
    }

    /**
     * ============================================================
     * JABATAN
     * ============================================================
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(
            Department::class,
            'department_id'
        );
    }

    /**
     * ============================================================
     * ROLE
     * ============================================================
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_user'
        )
        ->using(RoleUser::class)
        ->withTimestamps();
    }

    /**
     * ============================================================
     * MASJID / KARIAH
     * ============================================================
     */
    public function masjids(): BelongsToMany
    {
        return $this->belongsToMany(
            Masjid::class,
            'masjid_user'
        )
        ->using(MasjidUser::class)
        ->withTimestamps();
    }

    /**
     * ============================================================
     * SEMAK ROLE
     * ============================================================
     */
    public function hasRole($roleSlug): bool
    {
        return $this->roles()
            ->where('slug', $roleSlug)
            ->exists();
    }

    public function hasAnyRole(array $roleSlugs): bool
    {
        return $this->roles()
            ->whereIn('slug', $roleSlugs)
            ->exists();
    }
}