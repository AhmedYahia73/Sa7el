<?php

namespace Database\Seeders;

use App\Models\Gate;
use Illuminate\Database\Seeder;

class GateSeeder extends Seeder
{
    public function run(): void
    {
        $gates = [
            ['name' => 'Main Gate', 'location' => 'North Entrance', 'village_id' => 1, 'status' => 1],
            ['name' => 'Back Gate', 'location' => 'South Entrance', 'village_id' => 1, 'status' => 1],
            ['name' => 'Main Gate', 'location' => 'East Entrance', 'village_id' => 2, 'status' => 1],
        ];

        foreach ($gates as $gate) {
            Gate::create($gate);
        }
    }
}
