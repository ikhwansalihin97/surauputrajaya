<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Role extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['name', 'slug'];

    /**
     * Tetapan Automatik untuk Spatie Activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug']) // Medan yang dipantau perubahan
            ->logOnlyDirty() // Rakam hanya jika ada perubahan sahaja
            ->useLogName('peranan_sistem') // Kategori log khusus untuk Role
            ->setDescriptionForEvent(function(string $eventName) {
                return "Peranan sistem '{$this->name}' ({$this->slug}) telah {$eventName}";
            });
    }

    // Hubungan: 1 Role dimiliki oleh banyak User
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}