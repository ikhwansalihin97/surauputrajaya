<?php

namespace App\Http\Controllers\Surau;

use App\Http\Controllers\Controller;
use App\Mail\JemputanAgmMail;
use App\Models\EmailLog;
use App\Models\Surau;
use App\Models\SurauAgmJemputan;
use App\Models\SurauAgmMesyuarat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

/**
 * Jemputan Mesyuarat Agung Tahunan: senarai jemputan (nama & e-mel) dan penghantaran
 * e-mel jemputan oleh sistem, sama ada seorang demi seorang atau secara pukal.
 */
class JemputanAgmController extends Controller
{
    public function index(SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat);
        $mesyuarat->load(['sesi', 'jemputan']);

        return view('surau.agm.jemputan', compact('surau', 'mesyuarat'));
    }

    public function store(Request $request, SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat);

        // Abaikan baris yang kosong sepenuhnya
        $rows = collect($request->input('jemputan', []))
            ->filter(fn ($r) => filled($r['nama'] ?? null) || filled($r['email'] ?? null))
            ->map(fn ($r) => ['nama' => trim((string) ($r['nama'] ?? '')), 'email' => strtolower(trim((string) ($r['email'] ?? '')))])
            ->values();
        $request->merge(['jemputan' => $rows->all()]);

        $attributes = [];
        foreach ($rows as $i => $r) {
            $attributes["jemputan.$i.nama"]  = 'Nama (baris ' . ($i + 1) . ')';
            $attributes["jemputan.$i.email"] = 'E-mel (baris ' . ($i + 1) . ')';
        }

        $request->validate([
            'jemputan'         => ['required', 'array', 'min:1', 'max:100'],
            'jemputan.*.nama'  => ['required', 'string', 'max:255'],
            'jemputan.*.email' => ['required', 'email', 'max:255', 'distinct'],
        ], [
            'jemputan.required' => 'Sila isi sekurang-kurangnya seorang jemputan.',
            'jemputan.max'      => 'Maksimum 100 jemputan bagi setiap simpanan.',
            'required'          => ':attribute wajib diisi.',
            'email'             => ':attribute tidak sah.',
            'distinct'          => ':attribute berulang dalam senarai.',
            'max'               => ':attribute terlalu panjang.',
        ], $attributes);

        // E-mel yang telah ada dalam senarai jemputan mesyuarat ini
        $sedia = $mesyuarat->jemputan()->whereIn('email', $rows->pluck('email'))->pluck('email')->all();
        if ($sedia) {
            $errors = [];
            foreach ($rows as $i => $r) {
                if (in_array($r['email'], $sedia, true)) {
                    $errors["jemputan.$i.email"] = "E-mel {$r['email']} telah ada dalam senarai jemputan.";
                }
            }
            throw ValidationException::withMessages($errors);
        }

        DB::transaction(function () use ($mesyuarat, $rows) {
            foreach ($rows as $r) {
                $mesyuarat->jemputan()->create($r + ['created_by' => Auth::id()]);
            }
        });

        $this->log($surau, $mesyuarat, "{$rows->count()} jemputan ditambah");

        return redirect()->route('surau.agm.jemputan.index', $mesyuarat)
            ->with('success', "{$rows->count()} jemputan telah ditambah ke senarai. Klik Hantar untuk menghantar e-mel jemputan.");
    }

    public function destroy(SurauAgmMesyuarat $mesyuarat, SurauAgmJemputan $jemputan)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat, $jemputan);
        $jemputan->delete();

        $this->log($surau, $mesyuarat, "Jemputan {$jemputan->nama} ({$jemputan->email}) dibuang");

        return redirect()->route('surau.agm.jemputan.index', $mesyuarat)
            ->with('success', "{$jemputan->nama} telah dibuang daripada senarai jemputan.");
    }

    /** Hantar e-mel jemputan kepada seorang */
    public function hantar(SurauAgmMesyuarat $mesyuarat, SurauAgmJemputan $jemputan)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat, $jemputan);

        $berjaya = $this->hantarEmel($mesyuarat, $jemputan);
        $this->log($surau, $mesyuarat, "E-mel jemputan kepada {$jemputan->email} " . ($berjaya ? 'dihantar' : 'gagal dihantar'));

        return redirect()->route('surau.agm.jemputan.index', $mesyuarat)->with(
            $berjaya ? 'success' : 'error',
            $berjaya ? "E-mel jemputan telah dihantar kepada {$jemputan->nama} ({$jemputan->email})."
                     : "E-mel jemputan kepada {$jemputan->email} gagal dihantar. Sila cuba lagi."
        );
    }

    /** Hantar e-mel jemputan secara pukal kepada jemputan yang dipilih */
    public function hantarPukal(Request $request, SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat);

        $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer']], [
            'ids.required' => 'Sila pilih sekurang-kurangnya seorang jemputan untuk dihantar.',
        ]);

        $senarai = $mesyuarat->jemputan()->whereIn('id', $request->input('ids'))->get();
        if ($senarai->isEmpty()) {
            return back()->withErrors(['ids' => 'Jemputan yang dipilih tidak ditemui.']);
        }

        $gagal = $senarai->reject(fn ($j) => $this->hantarEmel($mesyuarat, $j));
        $berjaya = $senarai->count() - $gagal->count();

        $this->log($surau, $mesyuarat, "E-mel jemputan pukal: {$berjaya} dihantar, {$gagal->count()} gagal");

        $redirect = redirect()->route('surau.agm.jemputan.index', $mesyuarat);
        if ($berjaya) {
            $redirect->with('success', "E-mel jemputan telah dihantar kepada {$berjaya} orang.");
        }
        if ($gagal->isNotEmpty()) {
            $redirect->with('error', 'E-mel gagal dihantar kepada: ' . $gagal->pluck('email')->implode(', ') . '. Sila cuba lagi.');
        }

        return $redirect;
    }

    private function hantarEmel(SurauAgmMesyuarat $mesyuarat, SurauAgmJemputan $jemputan): bool
    {
        $mail = new JemputanAgmMail($mesyuarat, $jemputan, Auth::user());
        $subject = $mail->envelope()->subject;

        try {
            Mail::to($jemputan->email)->send($mail);
            $status = 'SENT';
            $error  = null;
        } catch (\Throwable $e) {
            Log::error("Gagal hantar jemputan AGM kepada {$jemputan->email}: " . $e->getMessage());
            $status = 'FAILED';
            $error  = $e->getMessage();
        }

        EmailLog::create([
            'application_id' => null,
            'mail_type'      => 'JEMPUTAN_AGM',
            'to_email'       => json_encode([$jemputan->email]),
            'subject'        => $subject,
            'status'         => $status,
            'error_message'  => $error,
            'sent_at'        => $status === 'SENT' ? now() : null,
        ]);

        $jemputan->update($status === 'SENT'
            ? ['status_hantar' => 'SENT', 'dihantar_at' => now(), 'bilangan_hantar' => $jemputan->bilangan_hantar + 1]
            : ['status_hantar' => 'FAILED']);

        return $status === 'SENT';
    }

    /** Mesyuarat (dan jemputan) mesti milik surau aktif pengguna */
    private function authorizeMesyuarat(SurauAgmMesyuarat $mesyuarat, ?SurauAgmJemputan $jemputan = null): Surau
    {
        $surau = Auth::user()->activeSurau();

        abort_unless($surau && (int) $mesyuarat->sesi?->surau_id === (int) $surau->id, 403, 'Rekod mesyuarat ini bukan milik surau anda.');
        abort_if($jemputan && (int) $jemputan->agm_mesyuarat_id !== (int) $mesyuarat->id, 404);

        return $surau;
    }

    private function log(Surau $surau, SurauAgmMesyuarat $mesyuarat, string $message): void
    {
        activity()->useLog('surau')->causedBy(Auth::user())->performedOn($surau)
            ->log("{$message} bagi Mesyuarat Agung Tahunan Bil. {$mesyuarat->bilangan} ({$mesyuarat->tahun}) surau '{$surau->nama_surau}'.");
    }
}
