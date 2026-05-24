<?php

namespace Database\Seeders;

use App\Models\MaintenanceType;
use Illuminate\Database\Seeder;

class MaintenanceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Plumbing', 'status' => 1],
            ['name' => 'Electrical', 'status' => 1],
            ['name' => 'Air Conditioning', 'status' => 1],
            ['name' => 'Carpentry', 'status' => 1],
            ['name' => 'Painting', 'status' => 1],
        ];

        foreach ($types as $type) {
            MaintenanceType::create($type);
        }
    }
}
