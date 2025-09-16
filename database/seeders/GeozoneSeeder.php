<?php

namespace Database\Seeders;

use App\Models\Geozone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GeozoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $geozones = [
            [
                'name' => 'Kantor Pusat',
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'radius' => 200,
                'is_active' => true,
            ],
            [
                'name' => 'Cabang Bandung',
                'latitude' => -6.914744,
                'longitude' => 107.609810,
                'radius' => 200,
                'is_active' => true,
            ],
            [
                'name' => 'Cabang Surabaya',
                'latitude' => -7.250445,
                'longitude' => 112.768845,
                'radius' => 200,
                'is_active' => true,
            ],
        ];

        foreach ($geozones as $zone) {
            Geozone::create($zone);
        }
    }
}
