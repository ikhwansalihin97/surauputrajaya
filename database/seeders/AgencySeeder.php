<?php

namespace Database\Seeders;

use App\Models\Agency;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. JAWI
        |--------------------------------------------------------------------------
        */

        $jawi = Agency::updateOrCreate(
            [
                'code' => 'JAWI',
            ],
            [
                'name' => 'Jabatan Agama Islam Wilayah Persekutuan',
                'description' => 'Jabatan yang menguruskan hal ehwal Islam di Wilayah Persekutuan.',
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 2. Jabatan / Bahagian JAWI
        |--------------------------------------------------------------------------
        */

        $jawi->departments()->updateOrCreate(
            [
                'code' => 'BPMPS',
            ],
            [
                'name' => 'Bahagian Pengurusan Masjid dan Surau',
                'description' => 'Bahagian yang menguruskan pengurusan masjid dan surau.',
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 3. Contoh Unit / Bahagian Tambahan
        |--------------------------------------------------------------------------
        */

        $jawi->departments()->updateOrCreate(
            [
                'code' => 'BPD',
            ],
            [
                'name' => 'Bahagian Pengurusan Dakwah',
                'description' => 'Bahagian yang menguruskan hal ehwal dakwah.',
                'is_active' => true,
            ]
        );

        $jawi->departments()->updateOrCreate(
            [
                'code' => 'BPK',
            ],
            [
                'name' => 'Bahagian Penguatkuasaan',
                'description' => 'Bahagian penguatkuasaan undang-undang syariah.',
                'is_active' => true,
            ]
        );
    }
}