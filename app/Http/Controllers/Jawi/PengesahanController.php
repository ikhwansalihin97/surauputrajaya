<?php

namespace App\Http\Controllers\Jawi;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationMeetingMinutes;
use App\Models\ApplicationConfirmation;
use App\Models\ApplicationAppointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\MinitMesyuaratDitolakMail;
use App\Mail\PermohonanDisahkanJawiMail;
use App\Mail\PermohonanDitolakJawiMail;
use App\Mail\TemujanjiLawatanTapakMail;
use Barryvdh\DomPDF\Facade\Pdf;


class PengesahanController extends Controller
{
    /**
     * ============================================================
     * 1. SENARAI SEMUA PERMOHONAN
     * ============================================================
     */
    public function index(Request $request)
    {
        $query = Application::with([
            'masjid',
            'masjidConfirmation',
            'jawiConfirmation',
            'zonConfirmation',
        ]);

        /*
        |--------------------------------------------------------------------------
        | CARIAN
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('reference_no', 'like', "%{$search}%")
                    ->orWhere('nama_surau', 'like', "%{$search}%")
                    ->orWhere('pemohon_nama', 'like', "%{$search}%");

            });
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where('status', $request->status);

        }


        /*
        |--------------------------------------------------------------------------
        | SENARAI
        |--------------------------------------------------------------------------
        */

        $applications = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();


        return view(
            'admin.jawi.pengesahan.index',
            compact('applications')
        );
    }


    /**
     * ============================================================
     * 2. DETAIL PERMOHONAN
     * ============================================================
     */
    public function show($id)
    {
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
            'appointment.ditetapkanOleh',
            'appointments',
            'appointments.ditetapkanOleh',
            'user',
        ])->findOrFail($id);


        return view(
            'admin.jawi.pengesahan.show',
            compact('application')
        );
    }


    /**
     * ============================================================
     * CETAK BORANG PERMOHONAN (PDF A4)
     * ============================================================
     */
    public function cetakPermohonan($id)
    {
        $application = Application::with([
            'committees',
            'documents',
            'meetingMinutes',
            'masjid',
            'confirmations',
            'masjidConfirmation.confirmedBy',
            'jawiConfirmation.confirmedBy',
            'zonConfirmation.confirmedBy',
            'appointment',
            'user',
        ])->findOrFail($id);

        $jataPath = public_path('images/jata-negara.png');
        $jataBase64 = '';
        if (file_exists($jataPath)) {
            $jataBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($jataPath));
        }

        $pdf = Pdf::loadView('pdf.borang_permohonan', compact('application', 'jataBase64'))
            ->setPaper('a4', 'portrait');

        $fileName = 'Borang_Permohonan_' . str_replace('/', '_', $application->reference_no) . '.pdf';

        return $pdf->stream($fileName);
    }


    /**
     * ============================================================
     * 3. SAHKAN / TOLAK MINIT MESYUARAT
     * ============================================================
     */
    public function sahkanMinit(Request $request, $minitId)
    {
        $request->validate([
            'status_pengesahan' => 'required|in:DISAHKAN,DITOLAK',
            'catatan'           => 'required|string',
        ]);


        /*
        |--------------------------------------------------------------------------
        | DAPATKAN MINIT
        |--------------------------------------------------------------------------
        */

        $minit = ApplicationMeetingMinutes::findOrFail($minitId);


        /*
        |--------------------------------------------------------------------------
        | DAPATKAN PERMOHONAN
        |--------------------------------------------------------------------------
        */

        $application = $minit->application;


        /*
        |--------------------------------------------------------------------------
        | CATATAN
        |--------------------------------------------------------------------------
        |
        | Catatan ini datang terus daripada pilihan Pentadbir JAWI
        | melalui SweetAlert.
        |
        */

        $catatan = trim($request->catatan);


        /*
        |--------------------------------------------------------------------------
        | KEMASKINI STATUS MINIT
        |--------------------------------------------------------------------------
        */

        $minit->status_pengesahan = $request->status_pengesahan;
        $minit->catatan_semakan   = $catatan;
        $minit->tarikh_disahkan   = now();

        $minit->save();


        /*
        |--------------------------------------------------------------------------
        | HANTAR EMAIL JIKA MINIT DITOLAK
        |--------------------------------------------------------------------------
        |
        | TO  : Pemohon
        | CC  : Pentadbir Sistem
        |
        */

        if (
            $request->status_pengesahan === 'DITOLAK'
            && $application
            && !empty($application->pemohon_email)
        ) {

            /*
            |--------------------------------------------------------------------------
            | EMAIL PEMOHON
            |--------------------------------------------------------------------------
            */

            $pemohonEmail = null;

            if (
                filter_var(
                    $application->pemohon_email,
                    FILTER_VALIDATE_EMAIL
                )
            ) {

                $pemohonEmail = $application->pemohon_email;
            }


            /*
            |--------------------------------------------------------------------------
            | DAPATKAN EMAIL PENTADBIR SISTEM
            |--------------------------------------------------------------------------
            */

            $adminEmails = User::whereHas('roles', function ($query) {

                $query->where('slug', 'admin');

            })
                ->where('is_active', true)
                ->whereNotNull('email')
                ->pluck('email')
                ->filter(function ($email) {

                    return filter_var(
                        $email,
                        FILTER_VALIDATE_EMAIL
                    );

                })
                ->unique()
                ->values()
                ->toArray();


            /*
            |--------------------------------------------------------------------------
            | ELAKKAN EMAIL PEMOHON DARIPADA CC
            |--------------------------------------------------------------------------
            */

            $adminEmails = array_values(
                array_filter(
                    $adminEmails,
                    function ($email) use ($pemohonEmail) {

                        return strtolower($email)
                            !== strtolower($pemohonEmail);

                    }
                )
            );

            // dd([
            //     'pemohon' => $pemohonEmail,
            //     'admin_cc' => $adminEmails,
            // ]);


            /*
            |--------------------------------------------------------------------------
            | HANTAR EMAIL
            |--------------------------------------------------------------------------
            */

            if ($pemohonEmail) {

                Mail::to($pemohonEmail)
                    ->cc($adminEmails)
                    ->send(
                        new MinitMesyuaratDitolakMail(
                            $application,
                            $minit,
                            $catatan
                        )
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | MESEJ
        |--------------------------------------------------------------------------
        */

        if ($request->status_pengesahan === 'DITOLAK') {

            $mesej =
                'Status minit mesyuarat ditukar kepada DITOLAK. '
                . 'E-mel pemakluman telah dihantar kepada pemohon '
                . 'dan CC kepada Pentadbir Sistem.';

        } else {

            $mesej =
                'Status minit mesyuarat telah dikemaskini kepada DISAHKAN.';
        }


        return back()->with('success', $mesej);
    }


    /**
     * ============================================================
     * SAHKAN / TOLAK PERMOHONAN DI PERINGKAT JAWI
     * ============================================================
     */
    public function sahkan(Request $request, $id)
    {
        $status = $request->input('status_jawi') ?? $request->input('status');

        if (!in_array($status, ['APPROVED', 'REJECTED'])) {
            return back()->with('error', 'Sila pilih keputusan JAWI terlebih dahulu (LULUS / SAHKAN atau TOLAK PERMOHONAN).');
        }

        $user = Auth::user();

        if (!$user) {
            abort(403, 'Anda perlu log masuk untuk membuat pengesahan.');
        }

        if (!$user->hasAnyRole(['jawi', 'admin', 'pentadbir jawi', 'pentadbir sistem'])) {
            abort(403, 'Anda tidak mempunyai peranan sebagai Pentadbir JAWI.');
        }

        $application = Application::findOrFail($id);

        $confirmation = ApplicationConfirmation::where('application_id', $application->id)
            ->where('peringkat', 'JAWI')
            ->first();

        if (!$confirmation) {
            $confirmation = new ApplicationConfirmation([
                'application_id' => $application->id,
                'peringkat'      => 'JAWI',
            ]);
        }

        $confirmation->status       = $status;
        $confirmation->confirmed_by = Auth::id();
        $confirmation->catatan      = $request->catatan_jawi;
        $confirmation->confirmed_at = now();
        $confirmation->save();

        if ($status === 'APPROVED') {
            $application->update(['status' => 'PENDING']);
        } else {
            $application->update(['status' => 'REJECTED']);
        }

        /*
        |--------------------------------------------------------------------------
        | EMAIL PENTADBIR SISTEM & PEMOHON & ZON
        |--------------------------------------------------------------------------
        */
        $adminEmails = User::whereHas('roles', function ($query) {
            $query->where('slug', 'admin');
        })
            ->where('is_active', true)
            ->whereNotNull('email')
            ->pluck('email')
            ->filter(fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()->values()->toArray();

        $pemohonEmail = (!empty($application->pemohon_email) && filter_var($application->pemohon_email, FILTER_VALIDATE_EMAIL))
            ? $application->pemohon_email : null;

        if ($status === 'APPROVED') {
            $zonEmails = User::whereHas('roles', function ($query) {
                $query->where('slug', 'zon');
            })
                ->where('is_active', true)
                ->whereNotNull('email')
                ->pluck('email')
                ->filter(fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
                ->unique()->values()->toArray();

            $ccEmails = $adminEmails;
            if ($pemohonEmail) { $ccEmails[] = $pemohonEmail; }
            $ccEmails = collect($ccEmails)->filter()->unique()->values()->toArray();
            $subject = 'Permohonan Menunggu Semakan Pentadbir Zon - ' . $application->reference_no;

            try {
                if (!empty($zonEmails)) {
                    $mail = Mail::to($zonEmails);
                    if (!empty($ccEmails)) { $mail->cc($ccEmails); }
                    $mail->send(new PermohonanDisahkanJawiMail($application));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gagal hantar email JAWI Lulus: ' . $e->getMessage());
            }

            $mesej = 'Permohonan telah disahkan di peringkat JAWI. E-mel pemakluman telah dihantar kepada Pentadbir Zon untuk tindakan selanjutnya.';
        } else {
            $subject = 'Permohonan Ditolak di Peringkat JAWI - ' . $application->reference_no;
            try {
                if ($pemohonEmail) {
                    $mail = Mail::to($pemohonEmail);
                    if (!empty($adminEmails)) { $mail->cc($adminEmails); }
                    $mail->send(new PermohonanDitolakJawiMail($application));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gagal hantar email JAWI Tolak: ' . $e->getMessage());
            }

            $mesej = 'Permohonan telah ditolak di peringkat JAWI. E-mel pemakluman telah dihantar kepada pemohon dan CC kepada Pentadbir Sistem.';
        }

        return back()->with('success', $mesej);
    }


    /**
     * ============================================================
     * 4. KEPUTUSAN PENTADBIR JAWI
     * ============================================================
     */
    public function updateStatus(Request $request, $id)
    {
        $status = $request->input('status_jawi') ?? $request->input('status');

        if (!in_array($status, ['APPROVED', 'REJECTED'])) {
            return back()->with(
                'error',
                'Sila pilih keputusan JAWI terlebih dahulu (LULUS / SAHKAN atau TOLAK PERMOHONAN).'
            );
        }

        $application = Application::with([
            'meetingMinutes',
            'jawiConfirmation',
        ])->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | SEMAK MINIT MESYUARAT SEBELUM LULUS
        |--------------------------------------------------------------------------
        */

        if ($status === 'APPROVED') {
            $hasDisahkanMinit = $application->meetingMinutes
                ->where('status_pengesahan', 'DISAHKAN')
                ->isNotEmpty();

            $hasPendingMinit = $application->meetingMinutes
                ->whereIn('status_pengesahan', ['DALAM_SEMAKAN', 'PENDING'])
                ->isNotEmpty();

            if ($application->meetingMinutes->isNotEmpty() && (!$hasDisahkanMinit || $hasPendingMinit)) {
                return back()->with(
                    'error',
                    'Gagal meluluskan permohonan! Sila pastikan sekurang-kurangnya satu fail Minit Mesyuarat di bahagian (A. Sahkan Minit Mesyuarat) telah disahkan terlebih dahulu.'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | KEMASKINI PENGESAHAN JAWI
        |--------------------------------------------------------------------------
        */

        $confirmation = $application->jawiConfirmation;

        if ($confirmation && in_array($confirmation->status, ['APPROVED', 'REJECTED'])) {
            return back()->with(
                'error',
                'Permohonan ini telah disahkan dan dikunci. Keputusan tidak boleh diubah semula.'
            );
        }

        if (!$confirmation) {
            $confirmation = $application->confirmations()->create([
                'peringkat' => 'JAWI',
            ]);
        }

        $confirmation->status = $status;
        $confirmation->catatan = $request->catatan_jawi;
        $confirmation->confirmed_at = now();

        if (auth()->check()) {
            $confirmation->confirmed_by = auth()->id();
        }

        $confirmation->save();


        /*
        |--------------------------------------------------------------------------
        | KEMASKINI STATUS UTAMA APPLICATION
        |--------------------------------------------------------------------------
        */

        if ($status === 'REJECTED') {
            $application->status = 'REJECTED';
        } elseif ($status === 'APPROVED') {
            $application->status = 'PENDING';
        }

        $application->save();


        return redirect()
            ->route('admin.jawi.pengesahan.show', $application->id)
            ->with(
                'success',
                'Keputusan Pentadbir JAWI berjaya disimpan.'
            );
    }


    /**
     * ============================================================
     * 5. UPLOAD MINIT MESYUARAT
     * ============================================================
     */
    public function uploadMinit(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'fail_minit'     => 'required|file|mimes:pdf|max:5000',
        ]);


        if ($request->hasFile('fail_minit')) {

            $path = $request
                ->file('fail_minit')
                ->store('minit_mesyuarat', 'public');


            ApplicationMeetingMinutes::create([
                'application_id'      => $request->application_id,
                'tahun_minit'         => date('Y'),
                'fail_path'           => $path,
                'status_pengesahan'   => 'DALAM_SEMAKAN',
            ]);
        }


        return back()->with(
            'success',
            'Minit mesyuarat berjaya dimuat naik.'
        );
    }


    /**
     * ============================================================
     * 6. TETAPKAN / KEMASKINI TEMUJANJI LAWATAN TAPAK
     * ============================================================
     */
    public function tetapkanTemujanji(Request $request, $id)
    {
        $request->validate([
            'tarikh_temujanji' => 'required|date',
            'masa_temujanji'   => 'required',
            'tempat_temujanji' => 'required|string|max:255',
            'catatan'          => 'nullable|string|max:1000',
        ]);

        $application = Application::with(['zonConfirmation', 'appointment'])->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | SEMAKAN PENGESAHAN ZON
        |--------------------------------------------------------------------------
        | Selagi pentadbir zon belum buat apa-apa pengesahan (APPROVED / REJECTED),
        | pentadbir JAWI boleh menetapkan/kemaskini temujanji baru.
        |
        */
        $zonConfirmation = $application->zonConfirmation;
        if ($zonConfirmation && in_array($zonConfirmation->status, ['APPROVED', 'REJECTED'])) {
            return back()->with(
                'error',
                'Temujanji tidak boleh diubah kerana Pentadbir Zon telah membuat pengesahan permohonan ini.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN TEMUJANJI BAHARU (REKOD BAHARU)
        |--------------------------------------------------------------------------
        */
        $appointment = ApplicationAppointment::create([
            'application_id'   => $application->id,
            'tarikh_temujanji' => $request->tarikh_temujanji,
            'masa_temujanji'   => $request->masa_temujanji,
            'tempat'           => $request->tempat_temujanji,
            'catatan'          => $request->catatan,
            'ditetapkan_oleh'  => Auth::id(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | HANTAR EMAIL PEMAKLUMAN TEMUJANJI
        |--------------------------------------------------------------------------
        | TO : Pemohon & Pentadbir Zon
        | CC : Pentadbir Sistem & Pentadbir JAWI
        |
        */
        $pemohonEmail = (!empty($application->pemohon_email) && filter_var($application->pemohon_email, FILTER_VALIDATE_EMAIL))
            ? $application->pemohon_email : null;

        $zonEmails = User::whereHas('roles', function ($q) {
            $q->where('slug', 'zon');
        })
            ->where('is_active', true)
            ->whereNotNull('email')
            ->pluck('email')
            ->filter(fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->unique()->values()->toArray();

        $toEmails = array_values(array_unique(array_filter(array_merge(
            $pemohonEmail ? [$pemohonEmail] : [],
            $zonEmails
        ))));

        $adminEmails = User::whereHas('roles', function ($q) {
            $q->where('slug', 'admin');
        })
            ->where('is_active', true)
            ->whereNotNull('email')
            ->pluck('email')
            ->filter(fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->unique()->values()->toArray();

        $jawiEmails = User::whereHas('roles', function ($q) {
            $q->where('slug', 'jawi');
        })
            ->where('is_active', true)
            ->whereNotNull('email')
            ->pluck('email')
            ->filter(fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->unique()->values()->toArray();

        if (Auth::check() && filter_var(Auth::user()->email, FILTER_VALIDATE_EMAIL)) {
            $jawiEmails[] = Auth::user()->email;
        }

        $ccEmails = array_values(array_diff(array_unique(array_filter(array_merge(
            $adminEmails,
            $jawiEmails
        ))), $toEmails));

        try {
            if (!empty($toEmails)) {
                $mail = Mail::to($toEmails);
                if (!empty($ccEmails)) {
                    $mail->cc($ccEmails);
                }
                $mail->send(new TemujanjiLawatanTapakMail($application, $appointment));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal hantar email Temujanji: ' . $e->getMessage());
        }

        return back()->with(
            'success',
            'Temujanji lawatan tapak telah berjaya ditetapkan! E-mel pemakluman telah dihantar kepada Pemohon, Pentadbir Zon, Pentadbir Sistem, dan Pentadbir JAWI.'
        );
    }
}