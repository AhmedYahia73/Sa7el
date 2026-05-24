<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Restaurant', 'status' => 1],
            ['name' => 'Pharmacy', 'status' => 1],
            ['name' => 'Supermarket', 'status' => 1],
            ['name' => 'Gym', 'status' => 1],
            ['name' => 'Cafe', 'status' => 1],
        ];

        foreach ($types as $type) {
            ServiceType::create($type);
        }
    }
}
