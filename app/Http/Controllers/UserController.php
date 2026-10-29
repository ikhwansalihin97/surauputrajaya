<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Agency;
use App\Models\Department;
use App\Models\Masjid;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * ============================================================
     * SENARAI PENGGUNA
     * ============================================================
     */
    public function index(Request $request)
    {
        $query = User::with([
            'roles',
            'agency',
            'department',
            'masjids',
        ]);

        // ========================================================
        // 1. CARIAN
        // ========================================================

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere(
                        'no_kad_pengenalan',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere('jawatan', 'like', "%{$search}%")
                    ->orWhere('gred', 'like', "%{$search}%")
                    ->orWhere(
                        'skim_perkhidmatan',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'no_telefon_pejabat',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'no_telefon_bimbit',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        // ========================================================
        // 2. FILTER ROLE
        // ========================================================

        if ($request->filled('role_id')) {

            $query->whereHas('roles', function ($q) use ($request) {

                $q->where('roles.id', $request->role_id);

            });
        }

        // ========================================================
        // 3. FILTER AGENSI
        // ========================================================

        if ($request->filled('agency_id')) {

            $query->where(
                'agency_id',
                $request->agency_id
            );
        }

        // ========================================================
        // 4. FILTER JABATAN
        // ========================================================

        if ($request->filled('department_id')) {

            $query->where(
                'department_id',
                $request->department_id
            );
        }

        // ========================================================
        // 5. FILTER STATUS
        // ========================================================

        if ($request->filled('status')) {

            if ($request->status === 'active') {

                $query->where('is_active', true);

            } elseif ($request->status === 'inactive') {

                $query->where('is_active', false);
            }
        }

        // ========================================================
        // 6. PER PAGE
        // ========================================================

        $perPage = (int) $request->input('per_page', 10);

        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $users = $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        // ========================================================
        // DATA UNTUK FILTER / MODAL
        // ========================================================

        $roles = Role::orderBy('name')->get();

        $agencies = Agency::where('is_active', true)
            ->with([
                'departments' => function ($query) {
                    $query->where('is_active', true)
                        ->orderBy('name');
                }
            ])
            ->orderBy('name')
            ->get();

        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get();

        $masjids = Masjid::where('is_active', true)
            ->orderBy('nama_masjid')
            ->get();

        return view(
            'admin.users.index',
            compact(
                'users',
                'roles',
                'agencies',
                'departments',
                'masjids'
            )
        );
    }


    /**
     * ============================================================
     * SIMPAN PENGGUNA BAHARU
     * ============================================================
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            // ----------------------------------------------------
            // MAKLUMAT PERIBADI
            // ----------------------------------------------------

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'no_kad_pengenalan' => [
                'required',
                'string',
                'max:20',
                'unique:users,no_kad_pengenalan',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'no_telefon_bimbit' => [
                'required',
                'string',
                'max:30',
            ],

            'no_telefon_pejabat' => [
                'nullable',
                'string',
                'max:30',
            ],

            // ----------------------------------------------------
            // PERKHIDMATAN
            // ----------------------------------------------------

            'jawatan' => [
                'required',
                'string',
                'max:255',
            ],

            'gred' => [
                'nullable',
                'string',
                'max:50',
            ],

            'skim_perkhidmatan' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status_perkhidmatan' => [
                'required',
                Rule::in([
                    'TETAP',
                    'KONTRAK',
                    'SAMBILAN',
                    'LAIN_LAIN',
                ]),
            ],

            'sambungan' => [
                'nullable',
                'string',
                'max:20',
            ],

            // ----------------------------------------------------
            // ORGANISASI
            // ----------------------------------------------------

            'agency_id' => [
                'nullable',
                'exists:agencies,id',
            ],

            'department_id' => [
                'nullable',
                'exists:departments,id',
            ],

            // ----------------------------------------------------
            // AKAUN
            // ----------------------------------------------------

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            // ----------------------------------------------------
            // ROLE
            // ----------------------------------------------------

            'roles' => [
                'required',
                'array',
                'min:1',
            ],

            'roles.*' => [
                'integer',
                'exists:roles,id',
            ],

            // ----------------------------------------------------
            // MASJID
            // ----------------------------------------------------

            'masjids' => [
                'nullable',
                'array',
            ],

            'masjids.*' => [
                'integer',
                'exists:masjids,id',
            ],
        ]);


        // ========================================================
        // SEMAK ROLE PENTADBIR MASJID
        // ========================================================

        $pentadbirMasjidRoleId = Role::where(
            'slug',
            'masjid'
        )->value('id');

        $isPentadbirMasjid = $pentadbirMasjidRoleId
            && in_array(
                $pentadbirMasjidRoleId,
                $validated['roles']
            );


        // ========================================================
        // CIPTA USER
        // ========================================================

        $user = User::create([

            'name' =>
                $validated['name'],

            'no_kad_pengenalan' =>
                $validated['no_kad_pengenalan'],

            'email' =>
                $validated['email'],

            'no_telefon_bimbit' =>
                $validated['no_telefon_bimbit'],

            'no_telefon_pejabat' =>
                $validated['no_telefon_pejabat'] ?? null,

            'jawatan' =>
                $validated['jawatan'],

            'gred' =>
                $validated['gred'] ?? null,

            'skim_perkhidmatan' =>
                $validated['skim_perkhidmatan'] ?? null,

            'status_perkhidmatan' =>
                $validated['status_perkhidmatan'],

            'sambungan' =>
                $validated['sambungan'] ?? null,

            'agency_id' =>
                $validated['agency_id'] ?? null,

            'department_id' =>
                $validated['department_id'] ?? null,

            'password' =>
                $validated['password'],

            'is_active' => true,
        ]);


        // ========================================================
        // ROLE
        // ========================================================

        $user->roles()->sync(
            $validated['roles']
        );


        // ========================================================
        // MASJID / KARIAH
        // HANYA UNTUK PENTADBIR MASJID
        // ========================================================

        if ($isPentadbirMasjid) {

            $user->masjids()->sync(
                $validated['masjids'] ?? []
            );

        } else {

            $user->masjids()->sync([]);
        }


        return back()->with(
            'success',
            'Pengguna baharu berjaya didaftarkan.'
        );
    }


    /**
     * ============================================================
     * KEMASKINI PENGGUNA
     * ============================================================
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'no_kad_pengenalan' => [
                'required',
                'string',
                'max:20',
                Rule::unique(
                    'users',
                    'no_kad_pengenalan'
                )->ignore($user->id),
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(
                    'users',
                    'email'
                )->ignore($user->id),
            ],

            'no_telefon_bimbit' => [
                'required',
                'string',
                'max:30',
            ],

            'no_telefon_pejabat' => [
                'nullable',
                'string',
                'max:30',
            ],

            'jawatan' => [
                'required',
                'string',
                'max:255',
            ],

            'gred' => [
                'nullable',
                'string',
                'max:50',
            ],

            'skim_perkhidmatan' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status_perkhidmatan' => [
                'required',
                Rule::in([
                    'TETAP',
                    'KONTRAK',
                    'SAMBILAN',
                    'LAIN_LAIN',
                ]),
            ],

            'sambungan' => [
                'nullable',
                'string',
                'max:20',
            ],

            'agency_id' => [
                'nullable',
                'exists:agencies,id',
            ],

            'department_id' => [
                'nullable',
                'exists:departments,id',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'roles' => [
                'required',
                'array',
                'min:1',
            ],

            'roles.*' => [
                'integer',
                'exists:roles,id',
            ],

            'masjids' => [
                'nullable',
                'array',
            ],

            'masjids.*' => [
                'integer',
                'exists:masjids,id',
            ],
        ]);


        // ========================================================
        // SEMAK ROLE PENTADBIR MASJID
        // ========================================================

        $pentadbirMasjidRoleId = Role::where(
            'slug',
            'masjid'
        )->value('id');

        $isPentadbirMasjid =
            $pentadbirMasjidRoleId !== null
            && in_array(
                (int) $pentadbirMasjidRoleId,
                array_map(
                    'intval',
                    $validated['roles']
                ),
                true
            );


        // ========================================================
        // UPDATE USER
        // ========================================================

        $user->fill([
            'name' => $validated['name'],

            'no_kad_pengenalan' =>
                $validated['no_kad_pengenalan'],

            'email' =>
                $validated['email'],

            'no_telefon_bimbit' =>
                $validated['no_telefon_bimbit'],

            'no_telefon_pejabat' =>
                $validated['no_telefon_pejabat'] ?? null,

            'jawatan' =>
                $validated['jawatan'],

            'gred' =>
                $validated['gred'] ?? null,

            'skim_perkhidmatan' =>
                $validated['skim_perkhidmatan'] ?? null,

            'status_perkhidmatan' =>
                $validated['status_perkhidmatan'],

            'sambungan' =>
                $validated['sambungan'] ?? null,

            'agency_id' =>
                $validated['agency_id'] ?? null,

            'department_id' =>
                $validated['department_id'] ?? null,
        ]);


        // ========================================================
        // PASSWORD
        // ========================================================

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }


        // ========================================================
        // SIMPAN
        // ========================================================

        $user->save();


        // ========================================================
        // ROLE
        // ========================================================

        $user->roles()->sync(
            $validated['roles']
        );


        // ========================================================
        // MASJID
        // ========================================================

        if ($isPentadbirMasjid) {

            $user->masjids()->sync(
                $validated['masjids'] ?? []
            );

        } else {

            $user->masjids()->sync([]);
        }


        // ========================================================
        // SELESAI
        // ========================================================

        return back()->with(
            'success',
            'Maklumat pengguna berjaya dikemaskini.'
        );
    }
}