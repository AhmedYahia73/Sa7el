<?php

namespace Database\Seeders;

use App\Models\Village;
use Illuminate\Database\Seeder;

class VillageSeeder extends Seeder
{
    public function run(): void
    {
        $villages = [
            [
                'name' => 'Blue Bay Village',
                'description' => 'A beautiful coastal village',
                'location' => 'North Coast, Egypt',
                'from' => '2024-06-01',
                'to' => '2024-09-30',
                'package_id' => 1,
                'zone_id' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Green Hills Village',
                'description' => 'A peaceful residential village',
                'location' => 'South Zone, Egypt',
                'from' => '2024-01-01',
                'to' => '2024-12-31',
                'package_id' => 2,
                'zone_id' => 2,
                'status' => 1,
            ],
        ];

        foreach ($villages as $village) {
            Village::create($village);
        }
    }
}
