<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Pagination\Paginator;
use App\Models\Application;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        
        // 2. AUTO-LOG (Spatie): Log Masuk Pengguna daripada Event Laravel
        Event::listen(Login::class, function ($event) {
            $role = $event->user->roles->first()->name ?? session('active_role') ?? 'Pengguna';
            
            activity()
                ->useLog('kawalan_keselamatan')
                ->causedBy($event->user)
                ->withProperties([
                    'email' => $event->user->email,
                    'role'  => $role,
                    'ip'    => request()->ip()
                ])
                ->log("Pengguna '{$event->user->name}' ({$event->user->email}) telah berjaya log masuk sebagai {$role}.");
        });

        // 3. AUTO-LOG (Spatie): Log Keluar Pengguna daripada Event Laravel
        Event::listen(Logout::class, function ($event) {
            if ($event->user) {
                activity()
                    ->useLog('kawalan_keselamatan')
                    ->causedBy($event->user)
                    ->withProperties([
                        'email' => $event->user->email,
                        'ip'    => request()->ip()
                    ])
                    ->log("Pengguna '{$event->user->name}' ({$event->user->email}) telah log keluar daripada sistem.");
            }
        });
    }
}