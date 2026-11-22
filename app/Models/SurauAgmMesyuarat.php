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

    protected $fillable = [
        'agm_sesi_id', 'bilangan', 'tahun', 'tarikh', 'masa', 'lokasi', 'minit_path', 'laporan_kewangan_path',
        'pentadbir_pilihan', 'pentadbir_calon', 'status_pengesahan', 'dihantar_pengesahan_at',
        'surat_no_rujukan', 'surat_tarikh', 'surat_ditandatangani_oleh', 'created_by',
    ];

    protected $casts = [
        'tarikh'                 => 'date',
        'pentadbir_calon'        => 'array',
        'dihantar_pengesahan_at' => 'datetime',
        'surat_tarikh'           => 'date',
    ];

    /** Status keseluruhan pengesahan JAWI (null = belum dihantar) */
    public const STATUS_PENGESAHAN = [
        'MENUNGGU'         => ['Menunggu Pengesahan JAWI', 'bg-warning text-dark', 'fa-hourglass-half'],
        'PERLU_PEMBETULAN' => ['Perlu Pembetulan', 'bg-danger', 'fa-rotate-left'],
        'DISAHKAN'         => ['Disahkan JAWI', 'bg-success', 'fa-circle-check'],
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

    public function penandatanganSurat()
    {
        return $this->belongsTo(User::class, 'surat_ditandatangani_oleh');
    }

    /** Surat Pentauliahan AJK boleh dicetak: Mesyuarat Bil. 1 yang telah disahkan JAWI */
    public function bolehCetakPentauliahan(): bool
    {
        return $this->bilangan === 1 && $this->status_pengesahan === 'DISAHKAN';
    }

    public function pengesahan()
    {
        return $this->hasMany(SurauAgmPengesahan::class, 'agm_mesyuarat_id')->orderByDesc('pusingan');
    }

    public function pengesahanTerkini()
    {
        return $this->hasOne(SurauAgmPengesahan::class, 'agm_mesyuarat_id')->latestOfMany('pusingan');
    }

    public function pertukaranPentadbir()
    {
        return $this->hasMany(SurauPertukaranPentadbir::class, 'agm_mesyuarat_id')->latest('id');
    }

    /** Item yang perlu disahkan JAWI: Bil. 1 = minit, AJK, pentadbir; Bil. 2 = minit, laporan kewangan */
    public function itemPengesahan(): array
    {
        return $this->bilangan === 1 ? ['minit', 'ajk', 'pentadbir'] : ['minit', 'laporan_kewangan'];
    }

    /** Item masih boleh diubah oleh surau: belum dihantar, atau ditolak JAWI dalam pusingan terkini */
    public function bolehUbah(string $item): bool
    {
        return match ($this->status_pengesahan) {
            null               => true,
            'PERLU_PEMBETULAN' => $this->pengesahanTerkini?->status($item) === 'DITOLAK',
            default            => false,
        };
    }

    /** Item yang ditolak JAWI dalam pusingan terkini (semasa PERLU_PEMBETULAN) */
    public function itemDitolak(): array
    {
        if ($this->status_pengesahan !== 'PERLU_PEMBETULAN' || !$this->pengesahanTerkini) {
            return [];
        }

        return array_values(array_filter($this->itemPengesahan(), fn ($i) => $this->pengesahanTerkini->status($i) === 'DITOLAK'));
    }

    /** Item ditolak yang belum dibetulkan */
    public function itemBelumDibetulkan(): array
    {
        return array_values(array_filter($this->itemDitolak(), fn ($i) => !$this->pengesahanTerkini->telahDibetulkan($i)));
    }

    /** Tandakan item ditolak sebagai telah dibetulkan selepas surau mengemaskininya */
    public function tandaDibetulkan(string $item): void
    {
        $p = $this->pengesahanTerkini;
        if (!in_array($item, $this->itemDitolak(), true) || $p->telahDibetulkan($item)) {
            return;
        }
        $p->update(['dibetulkan' => array_values(array_unique([...($p->dibetulkan ?? []), $item]))]);
    }

    /** Butiran mesyuarat dikunci semasa menunggu / selepas disahkan JAWI */
    public function dikunciPengesahan(): bool
    {
        return in_array($this->status_pengesahan, ['MENUNGGU', 'DISAHKAN'], true);
    }

    /** Boleh dihantar (atau dihantar semula) untuk pengesahan JAWI */
    public function bolehHantarPengesahan(): bool
    {
        return in_array($this->status_pengesahan, [null, 'PERLU_PEMBETULAN'], true);
    }

    public function labelStatusPengesahan(): array
    {
        return self::STATUS_PENGESAHAN[$this->status_pengesahan] ?? ['Belum Dihantar', 'bg-secondary', 'fa-paper-plane'];
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
            ->logOnly(['bilangan', 'tahun', 'tarikh', 'masa', 'lokasi', 'minit_path', 'laporan_kewangan_path', 'pentadbir_pilihan', 'status_pengesahan'])
            ->logOnlyDirty();
    }
}
