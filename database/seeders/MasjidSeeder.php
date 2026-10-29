<?php

namespace Database\Seeders;

use App\Models\Masjid;
use App\Models\User;
use Illuminate\Database\Seeder;

class MasjidSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Cipta Masjid
        |--------------------------------------------------------------------------
        */

        $masjidPutrajaya = Masjid::firstOrCreate(
            [
                'nama_masjid' => 'Masjid Putra',
            ],
            [
                'alamat' => 'Presint 1, 62502 Putrajaya, Malaysia',
                'no_tel' => '03-8888 1234',
                'is_active' => true,
            ]
        );

        $masjidTuankuMizan = Masjid::firstOrCreate(
            [
                'nama_masjid' => 'Masjid Tuanku Mizan Zainal Abidin',
            ],
            [
                'alamat' => 'Presint 3, 62100 Putrajaya, Malaysia',
                'no_tel' => '03-8888 5678',
                'is_active' => true,
            ]
        );

        $masjidMahmoodiah = Masjid::firstOrCreate(
            [
                'nama_masjid' => 'Masjid Mahmoodiah',
            ],
            [
                'alamat' => 'Jalan P18C, Presint 18, 62100 Putrajaya',
                'no_tel' => '60 19-732 2734',
                'is_active' => true,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | 2. Cari User
        |--------------------------------------------------------------------------
        */

        $masjidUser = User::where('email', 'masjid@surau.com')->first();

        $adminUser = User::where('email', 'admin@surau.com')->first();


        /*
        |--------------------------------------------------------------------------
        | 3. Hubungkan User dengan Masjid
        |--------------------------------------------------------------------------
        */

        if ($masjidUser) {

            // Imam Kariah -> Masjid Putra
            $masjidUser->masjids()->syncWithoutDetaching([
                $masjidPutrajaya->id,
            ]);

            // Imam Kariah -> Masjid Tuanku Mizan
            $masjidUser->masjids()->syncWithoutDetaching([
                $masjidTuankuMizan->id,
            ]);

            // Imam Kariah -> Masjid Mahmoodiah
            $masjidUser->masjids()->syncWithoutDetaching([
                $masjidMahmoodiah->id,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 4. Multi-role Admin -> Semua Masjid
        |--------------------------------------------------------------------------
        */

        if ($adminUser) {

            $adminUser->masjids()->syncWithoutDetaching([
                $masjidPutrajaya->id,
                $masjidTuankuMizan->id,
                $masjidMahmoodiah->id,
            ]);
        }
    }
}