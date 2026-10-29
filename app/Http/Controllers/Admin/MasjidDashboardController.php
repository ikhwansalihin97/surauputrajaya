<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class MasjidDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | DAPATKAN SEMUA MASJID YANG DIURUSKAN OLEH PENTADBIR
        |--------------------------------------------------------------------------
        */

        $masjids = $user->masjids()
            ->where('is_active', true)
            ->get();

        if ($masjids->isEmpty()) {
            $jumlahMasjid = 0;
            $jumlahPermohonan = 0;
            $menungguPengesahan = 0;
            $telahDisahkan = 0;
            $ditolak = 0;
            $permohonanTerkini = collect();
            $kategoriSurauCounts = collect();
            $monthlyApplications = collect();

            return view('dashboard.masjid', compact(
                'masjids',
                'jumlahMasjid',
                'jumlahPermohonan',
                'menungguPengesahan',
                'telahDisahkan',
                'ditolak',
                'permohonanTerkini',
                'kategoriSurauCounts',
                'monthlyApplications'
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | ID SEMUA MASJID YANG DIJAGA OLEH PENTADBIR
        |--------------------------------------------------------------------------
        */

        $masjidIds = $masjids->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | PERMOHONAN DI BAWAH SEMUA KARIAH MASJID
        |--------------------------------------------------------------------------
        */

        $applications = Application::whereIn(
            'masjid_id',
            $masjidIds
        );

        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */

        // Jumlah semua permohonan daripada semua kariah
        $jumlahPermohonan = (clone $applications)->count();

        // Menunggu pengesahan
        $menungguPengesahan = (clone $applications)
            ->whereDoesntHave('confirmations', function ($query) {
                $query->where('peringkat', 'MASJID')
                    ->whereIn('status', ['APPROVED', 'DISAHKAN', 'REJECTED', 'DITOLAK']);
            })
            ->count();

        // Telah disahkan
        $telahDisahkan = (clone $applications)
            ->whereHas('confirmations', function ($query) {
                $query->where('peringkat', 'MASJID')
                    ->whereIn('status', ['APPROVED', 'DISAHKAN']);
            })
            ->count();

        // Ditolak / perlu tindakan
        $ditolak = (clone $applications)
            ->whereHas('confirmations', function ($query) {
                $query->where('peringkat', 'MASJID')
                    ->whereIn('status', ['REJECTED', 'DITOLAK']);
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | PERMOHONAN TERKINI
        |--------------------------------------------------------------------------
        */

        $permohonanTerkini = (clone $applications)
            ->with([
                'masjidConfirmation',
                'confirmations',
                'masjid',
            ])
            ->latest()
            ->take(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | PECAHAN KATEGORI SURAU
        |--------------------------------------------------------------------------
        */

        $kategoriSurauCounts = (clone $applications)
            ->selectRaw("COALESCE(NULLIF(kategori_surau, ''), 'Lain-lain') as kategori, COUNT(*) as total")
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        /*
        |--------------------------------------------------------------------------
        | TREND PERMOHONAN BULANAN TAHUN SEMASA
        |--------------------------------------------------------------------------
        */

        $monthlyApplications = (clone $applications)
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->pluck('total', 'month');

        /*
        |--------------------------------------------------------------------------
        | JUMLAH KARIAH / MASJID
        |--------------------------------------------------------------------------
        */

        $jumlahMasjid = $masjids->count();

        /*
        |--------------------------------------------------------------------------
        | RETURN DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view('dashboard.masjid', compact(
            'masjids',
            'jumlahMasjid',
            'jumlahPermohonan',
            'menungguPengesahan',
            'telahDisahkan',
            'ditolak',
            'permohonanTerkini',
            'kategoriSurauCounts',
            'monthlyApplications'
        ));
    }
}