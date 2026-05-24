<?php

namespace Database\Seeders;

use App\Models\AdminPosition;
use Illuminate\Database\Seeder;

class AdminPositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['name' => 'Admin Manager', 'type' => 'admin', 'status' => 1],
            ['name' => 'Village Manager', 'type' => 'village', 'status' => 1],
            ['name' => 'Provider Manager', 'type' => 'provider', 'status' => 1],
            ['name' => 'Maintenance Manager', 'type' => 'maintenance_provider', 'status' => 1],
        ];

        foreach ($positions as $position) {
            AdminPosition::create($position);
        }
    }
}
