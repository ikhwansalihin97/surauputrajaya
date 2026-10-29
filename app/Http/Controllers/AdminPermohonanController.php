<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationConfirmation;
use App\Models\ApplicationMeetingMinutes;
use App\Models\User;
use App\Models\Role;
use App\Models\EmailLog;
use App\Mail\MinitMesyuaratDitolakMail;
use App\Mail\PermohonanDisahkanZonMail;
use App\Mail\PermohonanDitolakZonMail;
use App\Mail\PengaktifanAkaunPemohonMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminPermohonanController extends Controller
{
    
    // 1. Papar Senarai Semua Permohonan
    public function index(Request $request)
    {
        $query = Application::query();

        // 1. Carian Kata Kunci (Search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                ->orWhere('nama_surau', 'like', "%{$search}%")
                ->orWhere('pemohon_nama', 'like', "%{$search}%")
                ->orWhere('kariah_masjid', 'like', "%{$search}%");
            });
        }

        // 2. Penapis Status Permohonan
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. Penapis Kategori Surau (Jika ada)
        if ($request->filled('kategori')) {
            $query->where('kategori_surau', $request->kategori);
        }

        // 4. Rekod Per Halaman (Per Page)
        $perPage = $request->input('per_page', 10);

        // 5. Susunan Tarikh Terbaharu & Pagination Berserta Query String
        $applications = $query->orderBy('created_at', 'desc')
                            ->paginate($perPage)
                            ->withQueryString();

        return view('admin.permohonan.index', compact('applications'));
    }

    // 2. Papar Butiran Permohonan untuk Disemak
    public function show($id)
    {
        // Eager loading hubungan pengesahan, temujanji, dan dokumen
        $application = Application::with([
            'committees',
            'documents',
            'meetingMinutes',
            'masjid',
            'confirmations',
            'masjidConfirmation',
            'jawiConfirmation',
            'zonConfirmation',
            'appointment',
            'appointments',
            'appointments.ditetapkanOleh',
            'user',
        ])->findOrFail($id);

        return view('admin.permohonan.show', compact('application'));
    }

    // 1. Kaedah khusus untuk sahkan/tolak fail minit mesyuarat
    public function sahkanMinit(Request $request, $minitId)
    {
        $request->validate([
            'status_pengesahan' => 'required|in:DISAHKAN,DITOLAK',
            'catatan'           => 'nullable|string',
        ]);

        $minit = ApplicationMeetingMinutes::findOrFail($minitId);
        $minit->status_pengesahan = $request->status_pengesahan;
        $minit->catatan_semakan   = $request->catatan;
        $minit->tarikh_disahkan   = now();
        $minit->save();

        // 1. Dapatkan data permohonan utama
        $application = $minit->application;

        // 2. Hantar e-mel JIKA status DITOLAK
        if ($request->status_pengesahan === 'DITOLAK' && $application && $application->pemohon_email) {
            
            // Dapatkan senarai e-mel AJK lain (asingkan e-mel pemohon utama & buang e-mel yang kosong)
            $ccEmails = $application->committees
                ->pluck('email')
                ->filter(function ($email) use ($application) {
                    return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && $email !== $application->pemohon_email;
                })
                ->unique()
                ->toArray();

            // Sediakan penghantaran e-mel
            $mailable = Mail::to($application->pemohon_email);

            // Tambah CC jika ada e-mel AJK lain
            if (!empty($ccEmails)) {
                $mailable->cc($ccEmails);
            }

            // Hantar e-mel
            $mailable->send(new MinitMesyuaratDitolakMail($application, $minit, $request->catatan));
        }

        $mesej = $request->status_pengesahan === 'DITOLAK'
            ? "Status minit mesyuarat ditukar kepada DITOLAK. E-mel pemakluman telah dihantar kepada pemohon utama dan CC kepada AJK lain."
            : "Status minit mesyuarat telah dikemaskini kepada DISAHKAN.";

        return back()->with('success', $mesej);
    }

    // 2. Kelulusan / Pengesahan Pentadbir Zon Selepas Lawatan Tapak
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'      => 'required|in:APPROVED,REJECTED',
            'catatan_zon' => 'nullable|string',
        ]);

        $application = Application::with([
            'meetingMinutes',
            'committees',
            'appointment',
            'appointments',
            'confirmations',
            'masjidConfirmation',
            'jawiConfirmation',
            'zonConfirmation',
            'masjid',
        ])->findOrFail($id);

        // 1. Semakan khas jika pegawai meluluskan permohonan
        if ($request->status === 'APPROVED') {
            // A. Semak fail Minit Mesyuarat jika wujud
            if ($application->meetingMinutes->isNotEmpty()) {
                $hasDisahkanMinit = $application->meetingMinutes
                    ->where('status_pengesahan', 'DISAHKAN')
                    ->isNotEmpty();

                $hasPendingMinit = $application->meetingMinutes
                    ->whereIn('status_pengesahan', ['DALAM_SEMAKAN', 'PENDING'])
                    ->isNotEmpty();

                if (!$hasDisahkanMinit || $hasPendingMinit) {
                    return back()->with('error', 'Gagal meluluskan permohonan! Sila pastikan fail Minit Mesyuarat telah disahkan (DISAHKAN) terlebih dahulu.');
                }
            }

            // B. Semak sama ada temujanji lawatan tapak telah diatur / dibuat
            $hasAppointment = $application->appointment !== null || $application->appointments->isNotEmpty();
            if (!$hasAppointment) {
                return back()->with('error', 'Gagal meluluskan permohonan! Lawatan tapak belum diatur. Pentadbir Zon hanya boleh meluluskan permohonan selepas lawatan tapak dibuat.');
            }
        }

        $catatan = trim($request->catatan_zon ?? $request->catatan ?? $request->catatan_jawi ?? '');

        // 2. Simpan / kemaskini pengesahan peringkat ZON dalam application_confirmations
        $zonConfirmation = ApplicationConfirmation::where('application_id', $application->id)
            ->where('peringkat', 'ZON')
            ->first();

        if (!$zonConfirmation) {
            $zonConfirmation = new ApplicationConfirmation([
                'application_id' => $application->id,
                'peringkat'      => 'ZON',
            ]);
        }

        $zonConfirmation->status       = $request->status;
        $zonConfirmation->confirmed_by = Auth::id() ?? auth()->id();
        $zonConfirmation->catatan      = $catatan;
        $zonConfirmation->confirmed_at = now();
        $zonConfirmation->save();

        // 3. Kemaskini status utama Application
        $application->status = $request->status;
        $application->save();

        // 4. Tindakan selepas diluluskan (APPROVED)
        if ($request->status === 'APPROVED') {
            $pemohonEmail = trim($application->pemohon_email ?? '');
            $pemohonIc    = trim($application->pemohon_ic ?? '');

            // A. Daftarkan / Kemaskini Pengguna Pemohon
            $user = User::where(function ($q) use ($pemohonEmail, $pemohonIc) {
                if (!empty($pemohonEmail)) {
                    $q->where('email', $pemohonEmail);
                }
                if (!empty($pemohonIc)) {
                    $q->orWhere('no_kad_pengenalan', $pemohonIc);
                }
            })->first();

            $activationToken = Str::random(64);

            if (!$user) {
                $user = User::create([
                    'name'                => $application->pemohon_nama,
                    'no_kad_pengenalan'   => $pemohonIc,
                    'email'               => $pemohonEmail,
                    'no_telefon_bimbit'   => $application->pemohon_tel,
                    'jawatan'             => $application->pemohon_jawatan ?: 'Pentadbir Surau',
                    'status_perkhidmatan' => 'LAIN_LAIN',
                    'password'            => Hash::make('password123'),
                    'is_active'           => true,
                    'is_first_login'      => true,
                    'activation_token'    => $activationToken,
                ]);
            } else {
                $user->name             = $application->pemohon_nama;
                $user->is_active        = true;
                $user->is_first_login   = true;
                $user->activation_token = $activationToken;
                $user->save();
            }

            // Tetapkan peranan 'surau' (Pentadbir Surau)
            $surauRole = Role::firstOrCreate(
                ['slug' => 'surau'],
                ['name' => 'Pentadbir Surau']
            );
            $user->roles()->syncWithoutDetaching([$surauRole->id]);

            // Hubungkan ID pengguna ke dalam permohonan
            $application->user_id = $user->id;
            $application->save();

            // B. Hantar 2 E-mel Rasmi
            if (!empty($pemohonEmail) && filter_var($pemohonEmail, FILTER_VALIDATE_EMAIL)) {
                // Senarai kesemua pentadbir (admin, jawi, zon, masjid)
                $allAdminEmails = User::whereHas('roles', function ($q) {
                    $q->whereIn('slug', ['admin', 'jawi', 'zon', 'masjid']);
                })
                ->where('is_active', true)
                ->whereNotNull('email')
                ->pluck('email')
                ->filter(fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
                ->unique()
                ->reject(fn($e) => strtolower($e) === strtolower($pemohonEmail))
                ->values()
                ->toArray();

                // Senarai Pentadbir Sistem sahaja (admin)
                $systemAdminEmails = User::whereHas('roles', function ($q) {
                    $q->where('slug', 'admin');
                })
                ->where('is_active', true)
                ->whereNotNull('email')
                ->pluck('email')
                ->filter(fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
                ->unique()
                ->reject(fn($e) => strtolower($e) === strtolower($pemohonEmail))
                ->values()
                ->toArray();

                // -------------------------------------------------------------
                // 1. E-mel kelulusan permohonan kepada pemohon & CC kesemua pentadbir
                // -------------------------------------------------------------
                $subjectLulus = 'Permohonan Pendaftaran Surau Telah Diluluskan - ' . $application->reference_no;
                try {
                    $mailLulus = Mail::to($pemohonEmail);
                    if (!empty($allAdminEmails)) {
                        $mailLulus->cc($allAdminEmails);
                    }
                    $mailLulus->send(new PermohonanDisahkanZonMail($application, $catatan));

                    EmailLog::create([
                        'application_id' => $application->id,
                        'mail_type'      => 'ZON_LULUS',
                        'to_email'       => json_encode([$pemohonEmail]),
                        'cc_email'       => !empty($allAdminEmails) ? json_encode($allAdminEmails) : null,
                        'bcc_email'      => null,
                        'subject'        => $subjectLulus,
                        'status'         => 'SENT',
                        'sent_at'        => now(),
                    ]);
                } catch (\Exception $e) {
                    Log::error('Gagal menghantar emel kelulusan permohonan zon: ' . $e->getMessage());
                    EmailLog::create([
                        'application_id' => $application->id,
                        'mail_type'      => 'ZON_LULUS',
                        'to_email'       => json_encode([$pemohonEmail]),
                        'cc_email'       => !empty($allAdminEmails) ? json_encode($allAdminEmails) : null,
                        'bcc_email'      => null,
                        'subject'        => $subjectLulus,
                        'status'         => 'FAILED',
                        'error_message'  => $e->getMessage(),
                    ]);
                }

                // -------------------------------------------------------------
                // 2. E-mel link pengaktifan kepada pemohon, BCC kepada pentadbir sistem
                // -------------------------------------------------------------
                $subjectAktivasi = 'Pautan Pengaktifan Akaun Pentadbir Surau - ' . $application->nama_surau;
                try {
                    $activationUrl = url('/activate/' . $activationToken);
                    $mailAktivasi = Mail::to($pemohonEmail);
                    if (!empty($systemAdminEmails)) {
                        $mailAktivasi->bcc($systemAdminEmails);
                    }
                    $mailAktivasi->send(new PengaktifanAkaunPemohonMail($application, $user, $activationToken, $activationUrl, 'password123'));

                    EmailLog::create([
                        'application_id' => $application->id,
                        'mail_type'      => 'PENGAKTIFAN_AKAUN',
                        'to_email'       => json_encode([$pemohonEmail]),
                        'cc_email'       => null,
                        'bcc_email'      => !empty($systemAdminEmails) ? json_encode($systemAdminEmails) : null,
                        'subject'        => $subjectAktivasi,
                        'status'         => 'SENT',
                        'sent_at'        => now(),
                    ]);
                } catch (\Exception $e) {
                    Log::error('Gagal menghantar emel link pengaktifan akaun: ' . $e->getMessage());
                    EmailLog::create([
                        'application_id' => $application->id,
                        'mail_type'      => 'PENGAKTIFAN_AKAUN',
                        'to_email'       => json_encode([$pemohonEmail]),
                        'cc_email'       => null,
                        'bcc_email'      => !empty($systemAdminEmails) ? json_encode($systemAdminEmails) : null,
                        'subject'        => $subjectAktivasi,
                        'status'         => 'FAILED',
                        'error_message'  => $e->getMessage(),
                    ]);
                }
            }

            return redirect()->route('admin.permohonan.show', $application->id)
                ->with('success', 'Permohonan telah berjaya DILULUSKAN oleh Pentadbir Zon. Maklumat pemohon telah didaftarkan sebagai pengguna sistem, dan 2 e-mel (Kelulusan & Pautan Pengaktifan) telah dikeluarkan.');
        } else {
            // Status REJECTED:
            $pemohonEmail = trim($application->pemohon_email ?? '');
            if (!empty($pemohonEmail) && filter_var($pemohonEmail, FILTER_VALIDATE_EMAIL)) {
                $allAdminEmails = User::whereHas('roles', function ($q) {
                    $q->whereIn('slug', ['admin', 'jawi', 'zon', 'masjid']);
                })
                ->where('is_active', true)
                ->whereNotNull('email')
                ->pluck('email')
                ->filter(fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
                ->unique()
                ->reject(fn($e) => strtolower($e) === strtolower($pemohonEmail))
                ->values()
                ->toArray();

                try {
                    $mailTolak = Mail::to($pemohonEmail);
                    if (!empty($allAdminEmails)) {
                        $mailTolak->cc($allAdminEmails);
                    }
                    $mailTolak->send(new PermohonanDitolakZonMail($application));
                } catch (\Exception $e) {
                    Log::error('Gagal menghantar emel penolakan zon: ' . $e->getMessage());
                }
            }

            return redirect()->route('admin.permohonan.show', $application->id)
                ->with('success', 'Status permohonan telah dikemaskini kepada DITOLAK oleh Pentadbir Zon.');
        }
    }

    public function uploadMinit(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'fail_minit' => 'required|file|mimes:pdf|max:5000',
        ]);

        if ($request->hasFile('fail_minit')) {
            $path = $request->file('fail_minit')->store('minit_mesyuarat', 'public');

            \App\Models\ApplicationMeetingMinutes::create([
                'application_id' => $request->application_id,
                'tahun_minit' => date('Y'),
                'fail_path' => $path,
                'status_pengesahan' => 'DALAM_SEMAKAN'
            ]);
        }

        return back()->with('success', 'Minit mesyuarat berjaya dimuat naik.');
    }
}