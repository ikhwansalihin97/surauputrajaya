<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Dapatkan Agensi & Jabatan
        |--------------------------------------------------------------------------
        */

        $jawi = Agency::where('code', 'JAWI')->firstOrFail();

        $bpmps = Department::where('agency_id', $jawi->id)
            ->where('code', 'BPMPS')
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | 2. Cipta / Kemaskini Roles
        |--------------------------------------------------------------------------
        */

        $adminRole = Role::updateOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Pentadbir Sistem']
        );

        $masjidRole = Role::updateOrCreate(
            ['slug' => 'masjid'],
            ['name' => 'Pentadbir Masjid']
        );

        $jawiRole = Role::updateOrCreate(
            ['slug' => 'jawi'],
            ['name' => 'Pentadbir JAWI']
        );

        $zonRole = Role::updateOrCreate(
            ['slug' => 'zon'],
            ['name' => 'Pentadbir Pusat Zon']
        );

        $surauRole = Role::updateOrCreate(
            ['slug' => 'surau'],
            ['name' => 'Pentadbir Surau']
        );


        /*
        |--------------------------------------------------------------------------
        | 3. Pentadbir Sistem
        |--------------------------------------------------------------------------
        */

        $multiUser = User::updateOrCreate(
            [
                'email' => 'admin@surau.com',
            ],
            [
                'name' => 'Ustaz Ahmad',
                'no_kad_pengenalan' => '800101-14-1234',
                'jawatan' => 'Pegawai Teknologi Maklumat',
                'gred' => 'F9',
                'skim_perkhidmatan' => 'Pegawai Teknologi Maklumat',
                'status_perkhidmatan' => 'TETAP',
                'no_telefon_pejabat' => '03-8888 1000',
                'sambungan' => '1000',
                'no_telefon_bimbit' => '012-3456789',

                'agency_id' => $jawi->id,
                'department_id' => $bpmps->id,

                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        $multiUser->roles()->sync([
            $adminRole->id,
            $masjidRole->id,
            $jawiRole->id,
            $zonRole->id,
            $surauRole->id,
        ]);


        /*
        |--------------------------------------------------------------------------
        | 4. Pentadbir Surau
        |--------------------------------------------------------------------------
        */

        /* $surauUser = User::updateOrCreate(
            [
                'email' => 'ali@surau.com',
            ],
            [
                'name' => 'Ali bin Ahmad',
                'no_kad_pengenalan' => '850202-10-2345',
                'jawatan' => 'Pengerusi Jawatankuasa Surau',
                'gred' => null,
                'skim_perkhidmatan' => null,
                'status_perkhidmatan' => 'LAIN_LAIN',
                'no_telefon_pejabat' => null,
                'sambungan' => null,
                'no_telefon_bimbit' => '013-4567890',

                'agency_id' => null,
                'department_id' => null,

                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        $surauUser->roles()->sync([
            $surauRole->id,
        ]); */


        /*
        |--------------------------------------------------------------------------
        | 5. Pentadbir Masjid
        |--------------------------------------------------------------------------
        */

        $masjidUser = User::updateOrCreate(
            [
                'email' => 'masjid@surau.com',
            ],
            [
                'name' => 'Imam Kariah',
                'no_kad_pengenalan' => '780303-14-3456',
                'jawatan' => 'Imam Masjid',
                'gred' => null,
                'skim_perkhidmatan' => null,
                'status_perkhidmatan' => 'LAIN_LAIN',
                'no_telefon_pejabat' => '03-8888 1234',
                'sambungan' => '101',
                'no_telefon_bimbit' => '014-5678901',

                'agency_id' => null,
                'department_id' => null,

                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        $masjidUser->roles()->sync([
            $masjidRole->id,
        ]);


        /*
        |--------------------------------------------------------------------------
        | 6. Pentadbir Pusat Zon
        |--------------------------------------------------------------------------
        */

        $zonUser = User::updateOrCreate(
            [
                'email' => 'zon@surau.com',
            ],
            [
                'name' => 'Pegawai Zon',
                'no_kad_pengenalan' => '820404-10-4567',
                'jawatan' => 'Pegawai Hal Ehwal Islam',
                'gred' => 'S9',
                'skim_perkhidmatan' => 'Pegawai Hal Ehwal Islam',
                'status_perkhidmatan' => 'TETAP',
                'no_telefon_pejabat' => '03-8888 2000',
                'sambungan' => '2000',
                'no_telefon_bimbit' => '016-6789012',

                'agency_id' => $jawi->id,
                'department_id' => $bpmps->id,

                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        $zonUser->roles()->sync([
            $zonRole->id,
        ]);
    }
}