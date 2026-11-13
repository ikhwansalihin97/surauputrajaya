<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use App\Http\Controllers\PortalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermohonanController;
use App\Http\Controllers\AdminPermohonanController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivationController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Masjid\PengesahanController;
use App\Http\Controllers\Admin\MasjidDashboardController;
use App\Http\Controllers\Jawi\PengesahanController as JawiPengesahanController;
use App\Http\Controllers\Jawi\SurauController as JawiSurauController;
use App\Http\Controllers\Surau\KemaskiniController as SurauKemaskiniController;
use App\Http\Controllers\Surau\MaklumatController as SurauMaklumatController;
use App\Http\Controllers\Surau\JawatankuasaController as SurauJawatankuasaController;
use App\Http\Controllers\Surau\AgmController as SurauAgmController;
use App\Http\Controllers\Surau\JemputanAgmController as SurauJemputanAgmController;
use App\Http\Controllers\Surau\KehadiranAgmController as SurauKehadiranAgmController;
use App\Http\Controllers\Surau\KeputusanAgmController as SurauKeputusanAgmController;
use App\Http\Controllers\PertukaranPentadbirController;


/*
|--------------------------------------------------------------------------
| 1. PORTAL AWAM
|--------------------------------------------------------------------------
*/

Route::get('/', [PortalController::class, 'index'])
    ->name('portal');


/*
|--------------------------------------------------------------------------
| 2. PERMOHONAN AWAM
|--------------------------------------------------------------------------
*/

Route::prefix('permohonan')
    ->name('permohonan.')
    ->group(function () {

        Route::get('/', [PermohonanController::class, 'showForm'])
            ->name('form');

        Route::post('/', [PermohonanController::class, 'store'])
            ->name('store');

        Route::get('/semak', [PermohonanController::class, 'semakStatus'])
            ->name('semak');

        Route::post(
            '/{id}/upload-semula-minit',
            [PermohonanController::class, 'uploadSemulaMinit']
        )->name('uploadSemulaMinit');
    });


/*
|--------------------------------------------------------------------------
| 3. PENGAKTIFAN AKAUN
|--------------------------------------------------------------------------
*/

// Paparkan borang penetapan kata laluan
Route::get('/activate/{token}', [ActivationController::class, 'showForm'])
    ->name('account.activate');

// Proses simpan kata laluan & aktifkan akaun
Route::post('/activate/{token}', [ActivationController::class, 'setPassword'])
    ->name('account.activate.set');



/*
|--------------------------------------------------------------------------
| 4. AUTENTIKASI
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


/*
|--------------------------------------------------------------------------
| 5. MODUL PENTADBIRAN
| Semua route memerlukan authentication
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD UTAMA
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');

    // Serah tugas Pentadbir Surau: calon log masuk untuk menerima / menolak (tanpa sekatan peranan)
    Route::get('/serah-tugas', [PertukaranPentadbirController::class, 'index'])->name('pertukaran-pentadbir.index');
    Route::post('/serah-tugas/{pertukaran}/terima', [PertukaranPentadbirController::class, 'terima'])->name('pertukaran-pentadbir.terima');
    Route::post('/serah-tugas/{pertukaran}/tolak', [PertukaranPentadbirController::class, 'tolak'])->name('pertukaran-pentadbir.tolak');


    /*
    |--------------------------------------------------------------------------
    | TUKAR ROLE
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/switch-role',
        [AuthController::class, 'switchRole']
    )->name('switch.role');


    /*
    |--------------------------------------------------------------------------
    | PROFIL PENGGUNA
    |--------------------------------------------------------------------------
    */

    Route::prefix('profil')
        ->name('profile.')
        ->group(function () {

            Route::get(
                '/',
                [ProfileController::class, 'edit']
            )->name('edit');

            Route::put(
                '/',
                [ProfileController::class, 'update']
            )->name('update');

            Route::put(
                '/kata-laluan',
                [ProfileController::class, 'updatePassword']
            )->name('password');
        });


    /*
    |--------------------------------------------------------------------------
    | MAKLUMAT SURAU (PENTADBIR SURAU)
    |--------------------------------------------------------------------------
    | Kemaskini terus rekod rasmi surau (jadual suraus) tanpa kelulusan.
    */

    Route::middleware('role:surau')->group(function () {

        Route::get('/surau/maklumat', [SurauMaklumatController::class, 'edit'])
            ->name('surau.maklumat.edit');

        Route::put('/surau/maklumat', [SurauMaklumatController::class, 'update'])
            ->name('surau.maklumat.update');

        Route::get('/surau/jawatankuasa', [SurauJawatankuasaController::class, 'edit'])
            ->name('surau.jawatankuasa.edit');

        Route::put('/surau/jawatankuasa', [SurauJawatankuasaController::class, 'update'])
            ->name('surau.jawatankuasa.update');

        // Mesyuarat Agung Tahunan (AGM) mengikut sesi
        Route::get('/surau/agm', [SurauAgmController::class, 'index'])->name('surau.agm.index');
        Route::get('/surau/agm/rekod', [SurauAgmController::class, 'create'])->name('surau.agm.create');
        Route::post('/surau/agm', [SurauAgmController::class, 'store'])->name('surau.agm.store');
        Route::get('/surau/agm/{mesyuarat}/kemaskini', [SurauAgmController::class, 'edit'])->name('surau.agm.edit');
        Route::put('/surau/agm/{mesyuarat}', [SurauAgmController::class, 'update'])->name('surau.agm.update');
        Route::delete('/surau/agm/{mesyuarat}', [SurauAgmController::class, 'destroy'])->name('surau.agm.destroy');

        // Jemputan mesyuarat AGM (e-mel oleh sistem)
        Route::get('/surau/agm/{mesyuarat}/jemputan', [SurauJemputanAgmController::class, 'index'])->name('surau.agm.jemputan.index');
        Route::post('/surau/agm/{mesyuarat}/jemputan', [SurauJemputanAgmController::class, 'store'])->name('surau.agm.jemputan.store');
        Route::post('/surau/agm/{mesyuarat}/jemputan/hantar', [SurauJemputanAgmController::class, 'hantarPukal'])->name('surau.agm.jemputan.hantarPukal');
        Route::post('/surau/agm/{mesyuarat}/jemputan/{jemputan}/hantar', [SurauJemputanAgmController::class, 'hantar'])->name('surau.agm.jemputan.hantar');
        Route::delete('/surau/agm/{mesyuarat}/jemputan/{jemputan}', [SurauJemputanAgmController::class, 'destroy'])->name('surau.agm.jemputan.destroy');

        // Kehadiran mesyuarat AGM
        Route::get('/surau/agm/{mesyuarat}/kehadiran', [SurauKehadiranAgmController::class, 'index'])->name('surau.agm.kehadiran.index');
        Route::post('/surau/agm/{mesyuarat}/kehadiran', [SurauKehadiranAgmController::class, 'store'])->name('surau.agm.kehadiran.store');
        Route::post('/surau/agm/{mesyuarat}/kehadiran/jemputan', [SurauKehadiranAgmController::class, 'storeDariJemputan'])->name('surau.agm.kehadiran.dariJemputan');
        Route::delete('/surau/agm/{mesyuarat}/kehadiran/{kehadiran}', [SurauKehadiranAgmController::class, 'destroy'])->name('surau.agm.kehadiran.destroy');

        // Keputusan mesyuarat AGM (minit, laporan kewangan, AJK & Pentadbir Surau)
        Route::get('/surau/agm/{mesyuarat}/keputusan', [SurauKeputusanAgmController::class, 'index'])->name('surau.agm.keputusan.index');
        Route::post('/surau/agm/{mesyuarat}/keputusan/dokumen', [SurauKeputusanAgmController::class, 'dokumen'])->name('surau.agm.keputusan.dokumen');
        Route::post('/surau/agm/{mesyuarat}/keputusan/pentadbir', [SurauKeputusanAgmController::class, 'pentadbir'])->name('surau.agm.keputusan.pentadbir');
        Route::post('/surau/agm/{mesyuarat}/keputusan/pentadbir/hantar-semula', [SurauKeputusanAgmController::class, 'hantarSemula'])->name('surau.agm.keputusan.hantarSemula');
        Route::post('/surau/agm/{mesyuarat}/keputusan/pentadbir/batal', [SurauKeputusanAgmController::class, 'batal'])->name('surau.agm.keputusan.batal');

        // Tukar surau aktif (pengguna yang mentadbir beberapa surau)
        Route::post('/surau/pilih/{surau}', [SurauJawatankuasaController::class, 'pilih'])
            ->name('surau.pilih');
    });

    /*
    |--------------------------------------------------------------------------
    | KEMASKINI MAKLUMAT SURAU (PENTADBIR SURAU)
    |--------------------------------------------------------------------------
    | Draf kemaskini tidak mengubah rekod yang telah diluluskan. Ia hanya
    | menjadi rekod rasmi selepas dihantar & diluluskan semula.
    */

    Route::prefix('surau/kemaskini')
        ->middleware('role:surau')
        ->name('surau.kemaskini.')
        ->group(function () {

            Route::post(
                '/',
                [SurauKemaskiniController::class, 'start']
            )->name('start');

            Route::get(
                '/{id}',
                [SurauKemaskiniController::class, 'edit']
            )->name('edit');

            Route::post(
                '/{id}',
                [SurauKemaskiniController::class, 'update']
            )->name('update');

            Route::delete(
                '/{id}',
                [SurauKemaskiniController::class, 'destroy']
            )->name('destroy');
        });


    /*
    |--------------------------------------------------------------------------
    | 2FA
    |--------------------------------------------------------------------------
    */

    Route::prefix('2fa')
        ->name('2fa.')
        ->group(function () {

            Route::get(
                '/verify',
                [AuthController::class, 'show2faForm']
            )->name('index');

            Route::post(
                '/verify',
                [AuthController::class, 'verify2fa']
            )->name('verify');

            Route::get(
                '/setup',
                [AuthController::class, 'setup2fa']
            )->name('setup');

            Route::post(
                '/enable',
                [AuthController::class, 'enable2fa']
            )->name('enable');
        });


    /*
    |--------------------------------------------------------------------------
    | 6. ADMIN
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')
        ->name('admin.')
        ->group(function () {

            /*
            |--------------------------------------------------------------------------
            | AUDIT TRAIL
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/audit-trail',
                [AuditLogController::class, 'index']
            )->middleware('role:admin')->name('audit-logs.index');


            /*
            |--------------------------------------------------------------------------
            | PENGURUSAN USER
            |--------------------------------------------------------------------------
            */

            Route::prefix('users')
                ->middleware('role:admin')
                ->name('users.')
                ->group(function () {

                    Route::get(
                        '/',
                        [UserController::class, 'index']
                    )->name('index');

                    Route::post(
                        '/',
                        [UserController::class, 'store']
                    )->name('store');

                    Route::put(
                        '/{user}',
                        [UserController::class, 'update']
                    )->name('update');

                    Route::patch(
                        '/{user}/toggle-status',
                        [UserController::class, 'toggleStatus']
                    )->name('toggleStatus');
                });


            /*
            |--------------------------------------------------------------------------
            | SEMAKAN PERMOHONAN
            | Pentadbir Sistem & Pentadbir JAWI
            |--------------------------------------------------------------------------
            */

            Route::prefix('permohonan')
                ->middleware('role:admin,jawi')
                ->name('permohonan.')
                ->group(function () {

                    Route::get(
                        '/',
                        [AdminPermohonanController::class, 'index']
                    )->name('index');

                    Route::get(
                        '/{id}',
                        [AdminPermohonanController::class, 'show']
                    )->name('show');

                    Route::put(
                        '/{id}/status',
                        [AdminPermohonanController::class, 'updateStatus']
                    )->name('updateStatus');

                    Route::post(
                        '/{id}/sahkan',
                        [PermohonanController::class, 'sahkanPermohonan']
                    )->name('sahkan');

                    Route::put(
                        '/minit/{minitId}/sahkan',
                        [AdminPermohonanController::class, 'sahkanMinit']
                    )->name('sahkanMinit');
                });


            /*
            |--------------------------------------------------------------------------
            | UPLOAD MINIT MESYUARAT
            |--------------------------------------------------------------------------
            */

            Route::post(
                '/minit-mesyuarat/upload',
                [AdminPermohonanController::class, 'uploadMinit']
            )->middleware('role:surau')->name('minit.upload');


            /*
            |--------------------------------------------------------------------------
            | 7. PENTADBIR MASJID
            |--------------------------------------------------------------------------
            |
            | URL:
            | /admin/masjid/...
            |
            | Route name:
            | admin.masjid....
            |
            |--------------------------------------------------------------------------
            */

            Route::prefix('masjid')
                ->middleware('role:masjid')
                ->name('masjid.')
                ->group(function () {

                    /*
                    |--------------------------------------------------------------------------
                    | DASHBOARD MASJID
                    |--------------------------------------------------------------------------
                    |
                    | URL:
                    | /admin/masjid/dashboard
                    |
                    | Name:
                    | admin.masjid.dashboard
                    |--------------------------------------------------------------------------
                    */

                    Route::get(
                        '/dashboard',
                        [MasjidDashboardController::class, 'index']
                    )->name('dashboard');


                    /*
                    |--------------------------------------------------------------------------
                    | SENARAI PENGESAHAN
                    |--------------------------------------------------------------------------
                    |
                    | URL:
                    | /admin/masjid/pengesahan
                    |
                    | Name:
                    | admin.masjid.pengesahan.index
                    |--------------------------------------------------------------------------
                    */

                    Route::get(
                        '/pengesahan',
                        [PengesahanController::class, 'index']
                    )->name('pengesahan.index');


                    /*
                    |--------------------------------------------------------------------------
                    | DETAIL PERMOHONAN
                    |--------------------------------------------------------------------------
                    |
                    | URL:
                    | /admin/masjid/pengesahan/{id}
                    |
                    | Name:
                    | admin.masjid.pengesahan.show
                    |--------------------------------------------------------------------------
                    */

                    Route::get(
                        '/pengesahan/{id}',
                        [PengesahanController::class, 'show']
                    )->name('pengesahan.show');


                    /*
                    |--------------------------------------------------------------------------
                    | SAHKAN
                    |--------------------------------------------------------------------------
                    |
                    | URL:
                    | /admin/masjid/pengesahan/{id}/sahkan
                    |
                    | Name:
                    | admin.masjid.pengesahan.sahkan
                    |--------------------------------------------------------------------------
                    */

                    Route::post(
                        '/pengesahan/{id}/sahkan',
                        [PengesahanController::class, 'sahkan']
                    )->name('pengesahan.sahkan');


                    /*
                    |--------------------------------------------------------------------------
                    | TOLAK
                    |--------------------------------------------------------------------------
                    |
                    | URL:
                    | /admin/masjid/pengesahan/{id}/tolak
                    |
                    | Name:
                    | admin.masjid.pengesahan.tolak
                    |--------------------------------------------------------------------------
                    */

                    Route::post(
                        '/pengesahan/{id}/tolak',
                        [PengesahanController::class, 'tolak']
                    )->name('pengesahan.tolak');

                });

            


            /*
            |--------------------------------------------------------------------------
            | 8. PENTADBIR JAWI
            |--------------------------------------------------------------------------
            |
            | URL:
            | /admin/jawi/pengesahan
            |
            | Pentadbir JAWI boleh melihat SEMUA permohonan
            | dan status setiap peringkat pengesahan.
            |
            |--------------------------------------------------------------------------
            */

            Route::prefix('jawi')
                ->middleware('role:jawi')
                ->name('jawi.')
                ->group(function () {

                /*
                |--------------------------------------------------------------------------
                | SENARAI SEMUA PERMOHONAN
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/pengesahan',
                    [JawiPengesahanController::class, 'index']
                )->name('pengesahan.index');


                /*
                |--------------------------------------------------------------------------
                | DETAIL PERMOHONAN
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/pengesahan/{id}',
                    [JawiPengesahanController::class, 'show']
                )->name('pengesahan.show');

                Route::get(
                    '/pengesahan/{id}/cetak',
                    [JawiPengesahanController::class, 'cetakPermohonan']
                )->name('pengesahan.cetak');

                /*
                |--------------------------------------------------------------------------
                | SAHKAN / TOLAK MINIT MESYUARAT
                |--------------------------------------------------------------------------
                |
                | URL:
                | /admin/jawi/pengesahan/minit/{minitId}/sahkan
                |
                | Name:
                | admin.jawi.pengesahan.sahkanMinit
                |
                */

                Route::put(
                    '/pengesahan/minit/{minitId}/sahkan',
                    [JawiPengesahanController::class, 'sahkanMinit']
                )->name('pengesahan.sahkanMinit');


                /*
                |--------------------------------------------------------------------------
                | KEMASKINI / SIMPAN KEPUTUSAN JAWI
                |--------------------------------------------------------------------------
                */

                Route::put(
                    '/pengesahan/{id}/status',
                    [JawiPengesahanController::class, 'updateStatus']
                )->name('pengesahan.updateStatus');


                /*
                |--------------------------------------------------------------------------
                | TETAPKAN TEMUJANJI
                |--------------------------------------------------------------------------
                */

                Route::post(
                    '/pengesahan/{id}/temujanji',
                    [JawiPengesahanController::class, 'tetapkanTemujanji']
                )->name('pengesahan.temujanji');

                // Senarai & pendaftaran surau sedia ada
                Route::get('/surau', [JawiSurauController::class, 'index'])->name('surau.index');
                Route::get('/surau/daftar', [JawiSurauController::class, 'create'])->name('surau.create');
                Route::post('/surau', [JawiSurauController::class, 'store'])->name('surau.store');

                // Keputusan akhir selepas lawatan tapak (menggantikan Pentadbir Zon)
                Route::put(
                    '/pengesahan/{id}/keputusan-akhir',
                    [JawiPengesahanController::class, 'keputusanAkhir']
                )->name('pengesahan.keputusanAkhir');

                Route::post(
                    '/pengesahan/{id}/sahkan',
                    [JawiPengesahanController::class, 'sahkan']
                )->name('pengesahan.sahkan');

                Route::post(
                    '/pengesahan/{id}/tolak',
                    [JawiPengesahanController::class, 'tolak']
                )->name('pengesahan.tolak');

            });

        });

});