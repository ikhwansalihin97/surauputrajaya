<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    /**
     * ============================================================
     * SENARAI AUDIT LOG
     * ============================================================
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Hanya Pentadbir Sistem
        if (!$user || !$user->hasRole('admin')) {
            abort(403, 'Akses ditolak.');
        }

        /*
         * Spatie Activitylog
         *
         * causer = pengguna yang melakukan tindakan
         * subject = rekod/model yang terlibat
         */
        $query = Activity::query()
            ->with([
                'causer',
                'subject',
            ])
            ->latest();

        // ========================================================
        // 1. CARIAN
        // ========================================================

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'description',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'log_name',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'event',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'properties',
                    'like',
                    "%{$search}%"
                )

                // Cari nama/email causer
                ->orWhereHasMorph(
                    'causer',
                    [\App\Models\User::class],
                    function ($userQuery) use ($search) {

                        $userQuery
                            ->where(
                                'name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'email',
                                'like',
                                "%{$search}%"
                            );
                    }
                );
            });
        }

        // ========================================================
        // 2. FILTER MODUL
        // ========================================================

        if ($request->filled('log_name')) {

            $query->where(
                'log_name',
                $request->log_name
            );
        }

        // ========================================================
        // 3. FILTER EVENT
        // ========================================================

        if ($request->filled('event')) {

            $query->where(
                'event',
                $request->event
            );
        }

        // ========================================================
        // 4. PER PAGE
        // ========================================================

        $perPage = (int) $request->input(
            'per_page',
            10
        );

        if (!in_array(
            $perPage,
            [10, 25, 50, 100],
            true
        )) {
            $perPage = 10;
        }

        // ========================================================
        // 5. PAGINATION
        // ========================================================

        $logs = $query
            ->paginate($perPage)
            ->withQueryString();

        return view(
            'admin.audit.index',
            compact('logs')
        );
    }
}