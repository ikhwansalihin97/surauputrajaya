<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationMeetingMinutes;
use App\Models\ApplicationAppointment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard utama berdasarkan role aktif pengguna.
     */
    public function index()
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Dapatkan Role Aktif
        |--------------------------------------------------------------------------
        */

        $defaultRole = $user->roles->first()?->slug ?? 'surau';
        $activeRole = session('active_role', $defaultRole);

        /*
        |--------------------------------------------------------------------------
        | Dashboard Mengikut Role
        |--------------------------------------------------------------------------
        */

        switch ($activeRole) {

            
            /*
            |--------------------------------------------------------------------------
            | ADMIN SISTEM
            |--------------------------------------------------------------------------
            */

            case 'admin':

                $totalUsers = \App\Models\User::count();

                $activeUsers = \App\Models\User::where(
                    'is_active',
                    true
                )->count();

                $totalApplications = Application::count();

                $totalMasjids = \App\Models\Masjid::count();

                $totalAuditLogs = class_exists(
                    \Spatie\Activitylog\Models\Activity::class
                )
                    ? \Spatie\Activitylog\Models\Activity::count()
                    : 0;

                $rolesWithCount = \App\Models\Role::withCount('users')->get();


                /*
                |--------------------------------------------------------------------------
                | Carta Status Permohonan
                |--------------------------------------------------------------------------
                |
                | APPROVED + PENDING_ZON dikira sebagai Diluluskan
                |
                */

                $applicationStatusCounts = [
                    'pending' => Application::where(
                        'status',
                        'PENDING'
                    )->count(),

                    'approved' => Application::whereIn(
                        'status',
                        ['APPROVED', 'PENDING_ZON']
                    )->count(),

                    'rejected' => Application::where(
                        'status',
                        'REJECTED'
                    )->count(),
                ];


                /*
                |--------------------------------------------------------------------------
                | Carta Trend Permohonan Bulanan
                |--------------------------------------------------------------------------
                |
                | Mengambil jumlah permohonan bagi setiap bulan
                | berdasarkan created_at tahun semasa.
                |
                */

                $monthlyApplications = Application::selectRaw(
                    'MONTH(created_at) as month, COUNT(*) as total'
                )
                    ->whereYear(
                        'created_at',
                        now()->year
                    )
                    ->groupByRaw(
                        'MONTH(created_at)'
                    )
                    ->orderByRaw(
                        'MONTH(created_at)'
                    )
                    ->pluck(
                        'total',
                        'month'
                    );


                /*
                |--------------------------------------------------------------------------
                | 5 Permohonan Terkini
                |--------------------------------------------------------------------------
                */

                $recentApplications = Application::with('masjid')
                    ->latest()
                    ->take(5)
                    ->get();


                /*
                |--------------------------------------------------------------------------
                | 6 Audit Log Terkini
                |--------------------------------------------------------------------------
                */

                $recentAuditLogs = class_exists(
                    \Spatie\Activitylog\Models\Activity::class
                )
                    ? \Spatie\Activitylog\Models\Activity::with('causer')
                        ->latest()
                        ->take(6)
                        ->get()
                    : collect();


                return view(
                    'dashboard.admin',
                    compact(
                        'totalUsers',
                        'activeUsers',
                        'totalApplications',
                        'totalMasjids',
                        'totalAuditLogs',
                        'rolesWithCount',
                        'recentApplications',
                        'recentAuditLogs',
                        'applicationStatusCounts',
                        'monthlyApplications'
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | ADMIN JAWI
            |--------------------------------------------------------------------------
            */

            case 'jawi':

                $totalPermohonan = Application::count();

                $permohonanPending = Application::where(
                    'status',
                    'PENDING'
                )->count();

                $permohonanLulus = Application::whereIn(
                    'status',
                    ['APPROVED', 'PENDING_ZON']
                )->count();

                $permohonanTolak = Application::where(
                    'status',
                    'REJECTED'
                )->count();


                /*
                |--------------------------------------------------------------------------
                | Statistik Minit Mesyuarat (AGM)
                |--------------------------------------------------------------------------
                */

                $minitPending = ApplicationMeetingMinutes::where(
                    'status_pengesahan',
                    'DALAM_SEMAKAN'
                )->count();

                $minitDisahkan = ApplicationMeetingMinutes::where(
                    'status_pengesahan',
                    'DISAHKAN'
                )->count();

                $minitDitolak = ApplicationMeetingMinutes::where(
                    'status_pengesahan',
                    'DITOLAK'
                )->count();

                $totalMinit = ApplicationMeetingMinutes::count();


                /*
                |--------------------------------------------------------------------------
                | Statistik Temujanji Lawatan Tapak
                |--------------------------------------------------------------------------
                */

                $totalAppointments = ApplicationAppointment::count();

                $upcomingAppointments = ApplicationAppointment::with([
                    'application',
                    'ditetapkanOleh'
                ])
                    ->whereDate('tarikh_temujanji', '>=', now()->toDateString())
                    ->orderBy('tarikh_temujanji')
                    ->orderBy('masa_temujanji')
                    ->take(5)
                    ->get();


                /*
                |--------------------------------------------------------------------------
                | Carta 1: Trend Permohonan Bulanan Tahun Semasa (12 Bulan)
                |--------------------------------------------------------------------------
                */

                $monthlyApplicationsRaw = Application::selectRaw(
                    'MONTH(created_at) as month, COUNT(*) as total'
                )
                    ->whereYear('created_at', now()->year)
                    ->groupByRaw('MONTH(created_at)')
                    ->orderByRaw('MONTH(created_at)')
                    ->pluck('total', 'month')
                    ->toArray();

                $monthlyApplications = [];
                for ($m = 1; $m <= 12; $m++) {
                    $monthlyApplications[] = $monthlyApplicationsRaw[$m] ?? 0;
                }


                /*
                |--------------------------------------------------------------------------
                | Carta 2: Pecahan Mengikut Kategori Surau
                |--------------------------------------------------------------------------
                */

                $kategoriSurauCounts = Application::selectRaw(
                    'COALESCE(NULLIF(kategori_surau, ""), "Tidak Dinyatakan") as kategori, COUNT(*) as total'
                )
                    ->groupBy('kategori')
                    ->orderByDesc('total')
                    ->pluck('total', 'kategori');


                /*
                |--------------------------------------------------------------------------
                | Carta 3: Taraf Tapak Tanah Surau
                |--------------------------------------------------------------------------
                */

                $tarafTapakCounts = Application::selectRaw(
                    'COALESCE(NULLIF(taraf_tapak, ""), "Lain-lain") as taraf, COUNT(*) as total'
                )
                    ->groupBy('taraf')
                    ->orderByDesc('total')
                    ->pluck('total', 'taraf');


                /*
                |--------------------------------------------------------------------------
                | Carta 4: Saluran Peringkat Aliran Kerja (Workflow Pipeline)
                |--------------------------------------------------------------------------
                */

                $stageMasjid = Application::whereDoesntHave('confirmations', function ($q) {
                    $q->where('peringkat', 'MASJID')->where('status', 'APPROVED');
                })->where('status', '!=', 'REJECTED')->count();

                $stageJawi = Application::whereHas('confirmations', function ($q) {
                    $q->where('peringkat', 'MASJID')->where('status', 'APPROVED');
                })->whereDoesntHave('confirmations', function ($q) {
                    $q->where('peringkat', 'JAWI')->where('status', 'APPROVED');
                })->where('status', '!=', 'REJECTED')->count();

                $stageZon = Application::whereHas('confirmations', function ($q) {
                    $q->where('peringkat', 'JAWI')->where('status', 'APPROVED');
                })->whereDoesntHave('confirmations', function ($q) {
                    $q->where('peringkat', 'ZON')->where('status', 'APPROVED');
                })->where('status', '!=', 'REJECTED')->count();

                $stageCompleted = Application::where('status', 'APPROVED')->count();

                $workflowStages = [
                    'masjid'    => $stageMasjid,
                    'jawi'      => $stageJawi,
                    'zon'       => $stageZon,
                    'completed' => $stageCompleted,
                ];


                /*
                |--------------------------------------------------------------------------
                | Permohonan Terkini
                |--------------------------------------------------------------------------
                */

                $senaraiPermohonanTerkini = Application::with([
                    'meetingMinutes',
                    'masjidConfirmation',
                    'jawiConfirmation',
                    'zonConfirmation',
                    'masjid',
                    'appointment'
                ])
                    ->latest()
                    ->take(8)
                    ->get();


                return view(
                    'dashboard.jawi',
                    compact(
                        'totalPermohonan',
                        'permohonanPending',
                        'permohonanLulus',
                        'permohonanTolak',
                        'minitPending',
                        'minitDisahkan',
                        'minitDitolak',
                        'totalMinit',
                        'totalAppointments',
                        'upcomingAppointments',
                        'monthlyApplications',
                        'kategoriSurauCounts',
                        'tarafTapakCounts',
                        'workflowStages',
                        'senaraiPermohonanTerkini'
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | PENTADBIR MASJID
            |--------------------------------------------------------------------------
            |
            | Dashboard Masjid mempunyai controller sendiri:
            | MasjidDashboardController
            |
            */

            case 'masjid':

                return redirect()->route(
                    'admin.masjid.dashboard'
                );


            /*
            |--------------------------------------------------------------------------
            | PENTADBIR ZON
            |--------------------------------------------------------------------------
            */

            case 'zon':

                $totalPermohonan = Application::count();

                $menungguPengesahan = Application::where(function($q) {
                    $q->where('status', 'PENDING_ZON')
                      ->orWhereHas('confirmations', function ($cq) {
                          $cq->where('peringkat', 'JAWI')->where('status', 'APPROVED');
                      });
                })->whereDoesntHave('confirmations', function ($q) {
                    $q->where('peringkat', 'ZON');
                })->count();

                $telahDisahkan = Application::whereHas('confirmations', function ($q) {
                    $q->where('peringkat', 'ZON')->where('status', 'APPROVED');
                })->count();

                $ditolak = Application::whereHas('confirmations', function ($q) {
                    $q->where('peringkat', 'ZON')->where('status', 'REJECTED');
                })->count();

                $senaraiPermohonanTerkini = Application::with([
                    'confirmations',
                    'masjidConfirmation',
                    'jawiConfirmation',
                    'masjid'
                ])
                    ->latest()
                    ->take(5)
                    ->get();

                return view(
                    'dashboard.zon',
                    compact(
                        'totalPermohonan',
                        'menungguPengesahan',
                        'telahDisahkan',
                        'ditolak',
                        'senaraiPermohonanTerkini'
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | PENTADBIR SURAU
            |--------------------------------------------------------------------------
            */

            case 'surau':
                // Semak peranan aktif dari session
                $activeRole = session('active_role');

                if ($activeRole === 'admin') {
                    // ------------------------------------------------------------------
                    // LOGIK UTAMA UNTUK ADMIN: Ambil SEMUA permohonan yang DAH LULUS
                    // ------------------------------------------------------------------
                    $applications = Application::with([
                        'confirmations', 'masjidConfirmation', 'zonConfirmation',
                        'jawiConfirmation', 'meetingMinutes', 'masjid', 'appointment'
                    ])
                    ->where('status', 'LULUS') // Tukar 'LULUS' mengikut nama status sebenar dalam DB
                    ->latest()
                    ->get();

                    $application = null;
                    $minitList = collect();

                    return view('dashboard.surau', compact('applications', 'application', 'minitList'));

                } else {
                    // ------------------------------------------------------------------
                    // LOGIK ASAL UNTUK USER SURAU: Ambil 1 permohonan milik mereka sahaja
                    // Utamakan rekod rasmi (LULUS terkini) yang ditadbir oleh pengguna ini,
                    // supaya permohonan kemaskini yang belum lulus tidak menggantikannya.
                    // ------------------------------------------------------------------
                    $relations = [
                        'confirmations', 'masjidConfirmation', 'zonConfirmation',
                        'jawiConfirmation', 'meetingMinutes', 'masjid', 'appointment'
                    ];

                    $application = Application::where('user_id', $user->id)
                        ->where('status', 'APPROVED')
                        ->orderByDesc('id')
                        ->get()
                        ->map(fn ($app) => $app->currentApproved())
                        ->first(fn ($current) => $current && (int) $current->user_id === (int) $user->id)
                        ?->load($relations);

                    if (!$application) {
                        $application = Application::with($relations)
                            ->where('user_id', $user->id)
                            ->where('jenis', 'BARU')
                            ->first();
                    }

                    // Padankan mengikut e-mel atau IC pemohon jika belum dipautkan user_id
                    if (!$application) {
                        $application = Application::with([
                            'confirmations', 'masjidConfirmation', 'zonConfirmation',
                            'jawiConfirmation', 'meetingMinutes', 'masjid', 'appointment'
                        ])
                        ->where('jenis', 'BARU')
                        ->where(function ($q) use ($user) {
                            if ($user->email) {
                                $q->where('pemohon_email', $user->email);
                            }
                            if ($user->no_kad_pengenalan) {
                                $q->orWhere('pemohon_ic', $user->no_kad_pengenalan);
                            }
                        })
                        ->first();

                        if ($application && !$application->user_id) {
                            $application->user_id = $user->id;
                            $application->save();
                        }
                    }

                    $applications = collect(); // Kosong untuk user biasa
                    $minitList = $application
                        ? $application->meetingMinutes->sortByDesc('created_at')
                        : collect();

                    // Kemaskini maklumat terkini selepas rekod rasmi (DRAF / DALAM PROSES / DITOLAK)
                    $revision = ($application && $application->status === 'APPROVED')
                        ? Application::withDrafts()
                            ->chainOf($application->rootId())
                            ->where('jenis', 'KEMASKINI')
                            ->where('id', '>', $application->id)
                            ->orderByDesc('id')
                            ->first()
                        : null;

                    return view('dashboard.surau', compact('application', 'applications', 'minitList', 'revision'));
                };


            /*
            |--------------------------------------------------------------------------
            | ROLE TIDAK DIKENALI
            |--------------------------------------------------------------------------
            */

            default:

                abort(
                    403,
                    'Peranan pengguna tidak dikenali.'
                );
        }
    }


    /**
     * Muat Naik Minit Mesyuarat Tahunan (AGM)
     *
     * Fungsi ini untuk Pentadbir Surau.
     */
    public function uploadMinit(Request $request)
    {
        $request->validate([
            'application_id' => [
                'required',
                'exists:applications,id',
            ],

            'tahun_minit' => [
                'required',
                'numeric',
                'digits:4',
            ],

            'fail_minit' => [
                'required',
                'file',
                'mimes:pdf',
                'max:5000',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Simpan Fail
        |--------------------------------------------------------------------------
        */

        $path = $request
            ->file('fail_minit')
            ->store(
                'minit_mesyuarat',
                'public'
            );


        /*
        |--------------------------------------------------------------------------
        | Simpan Rekod Minit
        |--------------------------------------------------------------------------
        */

        ApplicationMeetingMinutes::create([
            'application_id' => $request->application_id,

            'tahun_minit' => $request->tahun_minit,

            'fail_path' => $path,

            'status_pengesahan' => 'DALAM_SEMAKAN',

            'uploaded_by' => auth()->id(),
        ]);


        /*
        |--------------------------------------------------------------------------
        | Kembali
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            'Minit Mesyuarat Agung bagi tahun '
            . $request->tahun_minit
            . ' berjaya dihantar untuk semakan JAWI.'
        );
    }
}