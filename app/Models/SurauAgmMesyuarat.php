<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Mesyuarat Agung Tahunan dalam sesi: Bil. 1 (tahun mula) atau Bil. 2 (tahun tamat).
 */
class SurauAgmMesyuarat extends Model
{
    use LogsActivity;

    protected $table = 'surau_agm_mesyuarat';

    protected $fillable = ['agm_sesi_id', 'bilangan', 'tahun', 'tarikh', 'masa', 'lokasi', 'minit_path', 'laporan_kewangan_path', 'created_by'];

    protected $casts = [
        'tarikh' => 'date',
    ];

    public function sesi()
    {
        return $this->belongsTo(SurauAgmSesi::class, 'agm_sesi_id');
    }

    public function jemputan()
    {
        return $this->hasMany(SurauAgmJemputan::class, 'agm_mesyuarat_id')->orderBy('id');
    }

    public function kehadiran()
    {
        return $this->hasMany(SurauAgmKehadiran::class, 'agm_mesyuarat_id')->orderBy('id');
    }

    /** Cth. "21 Ogos 2026 (Jumaat)" */
    public function tarikhMelayu(?\Carbon\Carbon $tarikh = null): string
    {
        $t = ($tarikh ?? $this->tarikh)->copy()->locale('ms');

        return $t->translatedFormat('j F Y') . ' (' . $t->translatedFormat('l') . ')';
    }

    /** Cth. "9.00 pagi", "2.30 petang", "8.30 malam" */
    public function masaMelayu(): string
    {
        $t = \Carbon\Carbon::parse($this->masa);
        $jam = (int) $t->format('G');
        $waktu = match (true) {
            $jam < 12 => 'pagi',
            $jam < 14 => 'tengah hari',
            $jam < 19 => 'petang',
            default   => 'malam',
        };

        return $t->format('g.i') . ' ' . $waktu;
    }

    /** Tarikh akhir pengesahan kehadiran: 2 hari sebelum mesyuarat (null jika telah berlalu) */
    public function tarikhAkhirPengesahan(): ?\Carbon\Carbon
    {
        $akhir = $this->tarikh->copy()->subDays(2)->setTime(12, 0);

        return $akhir->isFuture() ? $akhir : null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('surau')
            ->logOnly(['bilangan', 'tahun', 'tarikh', 'masa', 'lokasi', 'minit_path', 'laporan_kewangan_path'])
            ->logOnlyDirty();
    }
}
