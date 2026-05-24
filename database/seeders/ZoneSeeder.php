<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['name' => 'North Zone', 'description' => 'Northern coastal area', 'status' => 1],
            ['name' => 'South Zone', 'description' => 'Southern residential area', 'status' => 1],
            ['name' => 'East Zone', 'description' => 'Eastern commercial area', 'status' => 1],
        ];

        foreach ($zones as $zone) {
            Zone::create($zone);
        }
    }
}
