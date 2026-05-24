<?php

namespace Database\Seeders;

use App\Models\Appartment;
use Illuminate\Database\Seeder;

class AppartmentSeeder extends Seeder
{
    public function run(): void
    {
        $appartments = [
            ['unit' => 'A-101', 'location' => 'Block A, Floor 1', 'appartment_type_id' => 1, 'village_id' => 1, 'user_id' => 3],
            ['unit' => 'A-102', 'location' => 'Block A, Floor 1', 'appartment_type_id' => 2, 'village_id' => 1, 'user_id' => null],
            ['unit' => 'B-201', 'location' => 'Block B, Floor 2', 'appartment_type_id' => 3, 'village_id' => 1, 'user_id' => null],
            ['unit' => 'C-101', 'location' => 'Block C, Floor 1', 'appartment_type_id' => 2, 'village_id' => 2, 'user_id' => null],
            ['unit' => 'C-102', 'location' => 'Block C, Floor 1', 'appartment_type_id' => 4, 'village_id' => 2, 'user_id' => null],
        ];

        foreach ($appartments as $appartment) {
            Appartment::create($appartment);
        }
    }
}
