<?php

namespace App\Http\Controllers\Masjid;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationConfirmation;
use App\Models\User;
use App\Models\EmailLog;
use App\Mail\PermohonanDisahkanMasjidMail;
use App\Mail\PermohonanDitolakMasjidMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PengesahanController extends Controller
{
    /**
     * ============================================================
     * SENARAI PERMOHONAN
     * ============================================================
     *
     * Seorang Pentadbir Masjid boleh menguruskan
     * lebih daripada satu kariah masjid.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | DAPATKAN SEMUA MASJID YANG DIURUSKAN
        |--------------------------------------------------------------------------
        */

        $masjids = $user->masjids()
            ->where('is_active', true)
            ->get();

        if ($masjids->isEmpty()) {
            abort(
                403,
                'Anda tidak mempunyai masjid yang aktif untuk diuruskan.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ID SEMUA MASJID
        |--------------------------------------------------------------------------
        */

        $masjidIds = $masjids->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | QUERY PERMOHONAN
        |--------------------------------------------------------------------------
        */

        $query = Application::with([
                'confirmations',
                'masjid',
            ])
            ->whereIn('masjid_id', $masjidIds);

        /*
        |--------------------------------------------------------------------------
        | CARIAN
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('reference_no', 'like', "%{$search}%")
                    ->orWhere('nama_surau', 'like', "%{$search}%")
                    ->orWhere('kategori_surau', 'like', "%{$search}%")
                    ->orWhere('kariah_masjid', 'like', "%{$search}%");

            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS PENGESAHAN
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $status = $request->status;

            /*
            |--------------------------------------------------------------------------
            | BELUM DISAHKAN OLEH PENTADBIR MASJID
            |--------------------------------------------------------------------------
            */

            if ($status === 'BELUM_DISAHKAN') {

                $query->whereDoesntHave('confirmations', function ($q) {

                    $q->where('peringkat', 'MASJID');

                });

            }

            /*
            |--------------------------------------------------------------------------
            | TELAH DISAHKAN OLEH PENTADBIR MASJID
            |--------------------------------------------------------------------------
            */

            elseif ($status === 'DISAHKAN') {

                $query->whereHas('confirmations', function ($q) {

                    $q->where('peringkat', 'MASJID')
                        ->where('status', 'APPROVED');

                });

            }

            /*
            |--------------------------------------------------------------------------
            | DITOLAK OLEH PENTADBIR MASJID
            |--------------------------------------------------------------------------
            */

            elseif ($status === 'DITOLAK') {

                $query->whereHas('confirmations', function ($q) {

                    $q->where('peringkat', 'MASJID')
                        ->where('status', 'REJECTED');

                });

            }
        }

        /*
        |--------------------------------------------------------------------------
        | BILANGAN REKOD PER HALAMAN
        |--------------------------------------------------------------------------
        */

        $allowedPerPage = [10, 25, 50, 100];

        $perPage = (int) $request->get('per_page', 10);

        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $applications = $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.masjid.pengesahan.index',
            compact(
                'masjids',
                'applications'
            )
        );
    }


    /**
     * ============================================================
     * PAPARKAN MAKLUMAT LENGKAP PERMOHONAN
     * ============================================================
     */
    public function show($id)
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | DAPATKAN SEMUA MASJID YANG DIURUSKAN
        |--------------------------------------------------------------------------
        */

        $masjids = $user->masjids()
            ->where('is_active', true)
            ->get();

        if ($masjids->isEmpty()) {

            abort(
                403,
                'Akaun anda tidak mempunyai masjid yang aktif.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ID MASJID YANG DIBENARKAN
        |--------------------------------------------------------------------------
        */

        $masjidIds = $masjids->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | DAPATKAN PERMOHONAN
        |--------------------------------------------------------------------------
        */

        $application = Application::with([
            'committees',
            'documents',
            'meetingMinutes',
            'masjidConfirmation',
            'confirmations',
            'masjid',
        ])->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | SEMAK AKSES
        |--------------------------------------------------------------------------
        */

        if (!$masjidIds->contains((int) $application->masjid_id)) {

            abort(
                403,
                'Anda tidak mempunyai akses kepada permohonan ini.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DAPATKAN MASJID BAGI PERMOHONAN INI
        |--------------------------------------------------------------------------
        */

        $masjid = $masjids->firstWhere(
            'id',
            $application->masjid_id
        );

        /*
        |--------------------------------------------------------------------------
        | DAPATKAN PENGESAHAN MASJID
        |--------------------------------------------------------------------------
        */

        $confirmation = $application->masjidConfirmation;

        /*
        |--------------------------------------------------------------------------
        | PAPARKAN VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.masjid.pengesahan.show',
            compact(
                'application',
                'masjid',
                'masjids',
                'confirmation'
            )
        );
    }


    /**
     * ============================================================
     * SAHKAN / SOKONG PERMOHONAN
     * ============================================================
     */
    public function sahkan(Request $request, $id)
    {
        /*
        |--------------------------------------------------------------------------
        | SEMAK PENGGUNA
        |--------------------------------------------------------------------------
        |
        | Hanya pengguna yang mempunyai role Pentadbir Masjid
        | dan bukan Pentadbir Sistem boleh membuat pengesahan
        | di peringkat Masjid.
        |
        */

        $user = Auth::user();

        if (!$user) {

            abort(
                403,
                'Anda perlu log masuk untuk membuat pengesahan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PASTIKAN PENGGUNA ADALAH PENTADBIR MASJID
        |--------------------------------------------------------------------------
        */

        if (!$user->hasRole('masjid')) {

            abort(
                403,
                'Anda tidak mempunyai peranan sebagai Pentadbir Masjid.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DAPATKAN SEMUA MASJID DI BAWAH PENTADBIR
        |--------------------------------------------------------------------------
        */

        $masjidIds = $user
            ->masjids()
            ->where('is_active', true)
            ->pluck('masjids.id');


        if ($masjidIds->isEmpty()) {

            return back()->with(
                'error',
                'Maklumat masjid pentadbir tidak ditemui.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DAPATKAN PERMOHONAN
        |--------------------------------------------------------------------------
        */

        $application = Application::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | SEMAK AKSES KE PERMOHONAN
        |--------------------------------------------------------------------------
        |
        | Pentadbir Masjid hanya boleh menyokong permohonan
        | yang berada di bawah masjid yang ditadbir olehnya.
        |
        */

        if (!$masjidIds->contains((int) $application->masjid_id)) {

            abort(
                403,
                'Anda tidak mempunyai akses kepada permohonan ini.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SEMAK KEPUTUSAN PENTADBIR MASJID
        |--------------------------------------------------------------------------
        |
        | Jika sudah ada confirmation MASJID, keputusan tidak boleh
        | dibuat sekali lagi.
        |
        */

        $existingConfirmation = ApplicationConfirmation::where(
            'application_id',
            $application->id
        )
            ->where('peringkat', 'MASJID')
            ->first();


        if ($existingConfirmation) {

            $mesej = match ($existingConfirmation->status) {

                'APPROVED' =>
                    'Permohonan ini telah pun disokong oleh Pentadbir Masjid. Tindakan tidak boleh dibuat sekali lagi.',

                'REJECTED' =>
                    'Permohonan ini telah pun ditolak oleh Pentadbir Masjid. Tindakan tidak boleh dibuat sekali lagi.',

                default =>
                    'Permohonan ini telah mempunyai keputusan di peringkat Pentadbir Masjid.',
            };


            return redirect()
                ->route(
                    'admin.masjid.pengesahan.show',
                    $application->id
                )
                ->with(
                    'error',
                    $mesej
                );
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN PENGESAHAN PENTADBIR MASJID
        |--------------------------------------------------------------------------
        |
        | Peringkat 1:
        | PENTADBIR MASJID
        |
        */

        ApplicationConfirmation::create([

            'application_id' => $application->id,

            'peringkat' => 'MASJID',

            'status' => 'APPROVED',

            'confirmed_by' => Auth::id(),

            'catatan' => $request->catatan_masjid,

            'confirmed_at' => now(),

        ]);


        /*
        |--------------------------------------------------------------------------
        | KEMASKINI STATUS PERMOHONAN
        |--------------------------------------------------------------------------
        |
        | Pentadbir Masjid telah menyokong permohonan.
        |
        | Permohonan akan bergerak ke peringkat Pentadbir JAWI.
        |
        */

        $application->update([

            'status' => 'PENDING',

        ]);


        /*
        |--------------------------------------------------------------------------
        | DAPATKAN PENTADBIR JAWI
        |--------------------------------------------------------------------------
        |
        | Syarat:
        |
        | 1. Role = jawi
        | 2. Akaun aktif
        | 3. Email tersedia
        |
        */

        $jawiEmails = User::whereHas('roles', function ($query) {

            $query->where('slug', 'jawi');

        })
            ->where('is_active', true)
            ->whereNotNull('email')
            ->pluck('email')
            ->filter()
            ->unique()
            ->values()
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | DAPATKAN PENTADBIR SISTEM
        |--------------------------------------------------------------------------
        |
        | Role = admin
        |
        */

        $adminEmails = User::whereHas('roles', function ($query) {

            $query->where('slug', 'admin');

        })
            ->where('is_active', true)
            ->whereNotNull('email')
            ->pluck('email')
            ->filter()
            ->unique()
            ->values()
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | EMAIL PEMOHON
        |--------------------------------------------------------------------------
        */

        $pemohonEmail = null;

        if (
            !empty($application->pemohon_email) &&
            filter_var(
                $application->pemohon_email,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            $pemohonEmail = $application->pemohon_email;
        }


        /*
        |--------------------------------------------------------------------------
        | SUBJECT EMAIL
        |--------------------------------------------------------------------------
        |
        | Subject ini selaras dengan PermohonanDisahkanMasjidMail.
        |
        */

        $subject =
            'Permohonan Menunggu Semakan Pentadbir JAWI - '
            . $application->reference_no;


        /*
        |--------------------------------------------------------------------------
        | HANTAR EMAIL
        |--------------------------------------------------------------------------
        */

        try {

            /*
            |----------------------------------------------------------------------
            | PASTIKAN ADA PENTADBIR JAWI
            |----------------------------------------------------------------------
            */

            if (empty($jawiEmails)) {

                throw new \Exception(
                    'Tiada Pentadbir JAWI aktif dengan alamat e-mel yang sah.'
                );
            }


            /*
            |----------------------------------------------------------------------
            | BINA SENARAI CC
            |----------------------------------------------------------------------
            |
            | CC:
            | - Pentadbir Sistem
            | - Pemohon
            |
            */

            $ccEmails = array_merge(

                $adminEmails,

                $pemohonEmail
                    ? [$pemohonEmail]
                    : []

            );


            /*
            |----------------------------------------------------------------------
            | BUANG DUPLIKASI
            |----------------------------------------------------------------------
            */

            $ccEmails = collect($ccEmails)
                ->filter()
                ->unique()
                ->values()
                ->toArray();


            /*
            |----------------------------------------------------------------------
            | HANTAR EMAIL
            |----------------------------------------------------------------------
            */

            $mail = Mail::to($jawiEmails);


            if (!empty($ccEmails)) {

                $mail->cc($ccEmails);

            }


            $mail->send(

                new PermohonanDisahkanMasjidMail(
                    $application
                )

            );


            /*
            |----------------------------------------------------------------------
            | REKOD EMAIL BERJAYA
            |----------------------------------------------------------------------
            */

            EmailLog::create([

                'application_id' => $application->id,

                'mail_type' => 'MASJID_SOKONG',

                'to_email' => json_encode(
                    $jawiEmails
                ),

                'cc_email' => !empty($ccEmails)
                    ? json_encode($ccEmails)
                    : null,

                'bcc_email' => null,

                'subject' => $subject,

                'status' => 'SENT',

                'error_message' => null,

                'sent_at' => now(),

            ]);


        } catch (\Exception $e) {


            /*
            |--------------------------------------------------------------------------
            | REKOD EMAIL GAGAL
            |--------------------------------------------------------------------------
            */

            EmailLog::create([

                'application_id' => $application->id,

                'mail_type' => 'MASJID_SOKONG',

                'to_email' => json_encode(
                    $jawiEmails
                ),

                'cc_email' => !empty($ccEmails ?? [])
                    ? json_encode($ccEmails)
                    : null,

                'bcc_email' => null,

                'subject' => $subject,

                'status' => 'FAILED',

                'error_message' => $e->getMessage(),

                'sent_at' => null,

            ]);


            /*
            |--------------------------------------------------------------------------
            | LARAVEL LOG
            |--------------------------------------------------------------------------
            */

            Log::error(
                'Gagal menghantar e-mel sokongan Pentadbir Masjid: '
                . $e->getMessage(),
                [

                    'application_id' =>
                        $application->id,

                    'reference_no' =>
                        $application->reference_no,

                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'admin.masjid.pengesahan.show',
                $application->id
            )
            ->with(
                'success',
                'Permohonan telah berjaya disokong dan Pentadbir JAWI telah dimaklumkan melalui e-mel.'
            );
    }


    /**
     * ============================================================
     * TOLAK PERMOHONAN
     * ============================================================
     */
    public function tolak(Request $request, $id)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI CATATAN
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'catatan_masjid' => [
                'required',
                'string',
                'max:1000',
            ],

        ], [

            'catatan_masjid.required' =>
                'Sila nyatakan sebab penolakan.',

            'catatan_masjid.max' =>
                'Catatan tidak boleh melebihi 1,000 aksara.',

        ]);

        /*
        |--------------------------------------------------------------------------
        | DAPATKAN SEMUA MASJID PENTADBIR
        |--------------------------------------------------------------------------
        */

        $masjidIds = Auth::user()
            ->masjids()
            ->where('is_active', true)
            ->pluck('masjids.id');

        if ($masjidIds->isEmpty()) {

            return back()->with(
                'error',
                'Maklumat masjid pentadbir tidak ditemui.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DAPATKAN PERMOHONAN
        |--------------------------------------------------------------------------
        */

        $application = Application::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | SEMAK AKSES
        |--------------------------------------------------------------------------
        */

        if (!$masjidIds->contains((int) $application->masjid_id)) {

            abort(
                403,
                'Anda tidak mempunyai akses kepada permohonan ini.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SEMAK KEPUTUSAN PENTADBIR MASJID
        |--------------------------------------------------------------------------
        |
        | Jika sudah ada confirmation MASJID, bermaksud Pentadbir Masjid
        | telah pun membuat keputusan.
        |
        | Tidak boleh Sokong/Tolak kali kedua.
        |
        */

        $existingConfirmation = ApplicationConfirmation::where(
            'application_id',
            $application->id
        )
            ->where('peringkat', 'MASJID')
            ->first();

        if ($existingConfirmation) {

            $mesej = match ($existingConfirmation->status) {
                'APPROVED' =>
                    'Permohonan ini telah pun disokong oleh Pentadbir Masjid. Tindakan tidak boleh dibuat sekali lagi.',

                'REJECTED' =>
                    'Permohonan ini telah pun ditolak oleh Pentadbir Masjid. Tindakan tidak boleh dibuat sekali lagi.',

                default =>
                    'Permohonan ini telah mempunyai keputusan di peringkat Pentadbir Masjid.',
            };

            return redirect()
                ->route(
                    'admin.masjid.pengesahan.show',
                    $application->id
                )
                ->with('error', $mesej);
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN REKOD PENGESAHAN
        |--------------------------------------------------------------------------
        |
        | Peringkat 1:
        | PENTADBIR MASJID
        |
        */

        ApplicationConfirmation::create([

            'application_id' => $application->id,

            'peringkat' => 'MASJID',

            'status' => 'REJECTED',

            'confirmed_by' => Auth::id(),

            'catatan' => $request->catatan_masjid,

            'confirmed_at' => now(),

        ]);

        /*
        |--------------------------------------------------------------------------
        | KEMASKINI STATUS KESELURUHAN PERMOHONAN
        |--------------------------------------------------------------------------
        |
        | Jika Masjid menolak, permohonan berhenti di sini.
        |
        */

        $application->update([
            'status' => 'REJECTED',
        ]);

        /*
        |--------------------------------------------------------------------------
        | HANTAR E-MEL KEPADA PEMOHON
        |--------------------------------------------------------------------------
        */

        try {

            /*
            |--------------------------------------------------------------------------
            | SEMAK EMAIL PEMOHON
            |--------------------------------------------------------------------------
            */

            if (
                empty($application->pemohon_email) ||
                !filter_var(
                    $application->pemohon_email,
                    FILTER_VALIDATE_EMAIL
                )
            ) {

                throw new \Exception(
                    'Alamat e-mel pemohon tidak sah atau tidak tersedia.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | SUBJECT EMAIL
            |--------------------------------------------------------------------------
            */

            $subject =
                'Permohonan Pendaftaran Surau Ditolak - '
                . $application->reference_no;

            /*
            |--------------------------------------------------------------------------
            | HANTAR EMAIL
            |--------------------------------------------------------------------------
            */

            Mail::to(
                $application->pemohon_email
            )->send(
                new PermohonanDitolakMasjidMail($application)
            );

            /*
            |--------------------------------------------------------------------------
            | REKOD EMAIL BERJAYA
            |--------------------------------------------------------------------------
            */

            \App\Models\EmailLog::create([

                'application_id' => $application->id,

                'mail_type' => 'MASJID_TOLAK',

                'to_email' => json_encode([
                    $application->pemohon_email
                ]),

                'cc_email' => null,

                'bcc_email' => null,

                'subject' => $subject,

                'status' => 'SENT',

                'error_message' => null,

                'sent_at' => now(),

            ]);

        } catch (\Exception $e) {

            /*
            |--------------------------------------------------------------------------
            | REKOD EMAIL GAGAL
            |--------------------------------------------------------------------------
            */

            \App\Models\EmailLog::create([

                'application_id' => $application->id,

                'mail_type' => 'MASJID_TOLAK',

                'to_email' => json_encode([

                    $application->pemohon_email ?? null

                ]),

                'cc_email' => null,

                'bcc_email' => null,

                'subject' =>
                    $subject ??
                    'Permohonan Pendaftaran Surau Ditolak',

                'status' => 'FAILED',

                'error_message' => $e->getMessage(),

                'sent_at' => null,

            ]);

            /*
            |--------------------------------------------------------------------------
            | LARAVEL LOG
            |--------------------------------------------------------------------------
            */

            Log::error(
                'Gagal menghantar e-mel penolakan Pentadbir Masjid: '
                . $e->getMessage(),
                [

                    'application_id' =>
                        $application->id,

                    'reference_no' =>
                        $application->reference_no,

                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | KEMBALI KE DETAIL
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'admin.masjid.pengesahan.show',
                $application->id
            )
            ->with(
                'success',
                'Permohonan telah ditolak dan pemohon telah dimaklumkan melalui e-mel.'
            );
    }
}