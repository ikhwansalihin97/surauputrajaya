<?php

namespace App\Http\Controllers;

use App\Models\SurauPertukaranPentadbir;
use App\Services\PertukaranPentadbirService;
use Illuminate\Support\Facades\Auth;

/**
 * Halaman dalam sistem untuk calon (pengguna log masuk) menerima atau menolak serah tugas Pentadbir Surau.
 */
class PertukaranPentadbirController extends Controller
{
    public function __construct(private PertukaranPentadbirService $service)
    {
    }

    public function index()
    {
        $user = Auth::user();
        session(['serah_tugas_dimaklum' => true]);

        $menunggu = SurauPertukaranPentadbir::with(['surau.masjid', 'dari'])
            ->where('kepada_user_id', $user->id)->aktif()->latest('id')->get();

        $terdahulu = SurauPertukaranPentadbir::with('surau')
            ->where('kepada_user_id', $user->id)
            ->where(fn ($q) => $q->where('status', '!=', 'MENUNGGU')->orWhere('tamat_at', '<=', now()))
            ->latest('updated_at')->limit(10)->get();

        $adaPeranan = $user->roles()->exists();

        return view('pertukaran-pentadbir.index', compact('menunggu', 'terdahulu', 'adaPeranan'));
    }

    public function terima(SurauPertukaranPentadbir $pertukaran)
    {
        $this->service->terima($pertukaran, Auth::user());

        session(['active_role' => 'surau', 'active_surau_id' => $pertukaran->surau_id]);

        return redirect()->route('dashboard')
            ->with('success', "Serah tugas diterima. Anda kini Pentadbir Surau bagi {$pertukaran->surau->nama_surau}.");
    }

    public function tolak(SurauPertukaranPentadbir $pertukaran)
    {
        $this->service->tolak($pertukaran, Auth::user());

        return redirect()->route('pertukaran-pentadbir.index')
            ->with('success', "Serah tugas bagi {$pertukaran->surau->nama_surau} telah ditolak. Pentadbir surau semasa telah dimaklumkan.");
    }
}
