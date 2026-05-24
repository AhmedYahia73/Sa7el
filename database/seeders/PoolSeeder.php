<?php

namespace Database\Seeders;

use App\Models\Pools;
use Illuminate\Database\Seeder;

class PoolSeeder extends Seeder
{
    public function run(): void
    {
        $pools = [
            ['name' => 'Main Pool', 'from' => '07:00', 'to' => '20:00', 'village_id' => 1, 'status' => 1],
            ['name' => 'Kids Pool', 'from' => '08:00', 'to' => '18:00', 'village_id' => 1, 'status' => 1],
            ['name' => 'Olympic Pool', 'from' => '06:00', 'to' => '22:00', 'village_id' => 2, 'status' => 1],
        ];

        foreach ($pools as $pool) {
            Pools::create($pool);
        }
    }
}
