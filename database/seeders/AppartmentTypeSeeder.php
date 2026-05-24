<?php

namespace Database\Seeders;

use App\Models\AppartmentType;
use Illuminate\Database\Seeder;

class AppartmentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Studio', 'status' => 1],
            ['name' => 'Apartment', 'status' => 1],
            ['name' => 'Villa', 'status' => 1],
            ['name' => 'Chalet', 'status' => 1],
            ['name' => 'Duplex', 'status' => 1],
        ];

        foreach ($types as $type) {
            AppartmentType::create($type);
        }
    }
}
