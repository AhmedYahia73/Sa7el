<?php

namespace Database\Seeders;

use App\Models\Beach;
use Illuminate\Database\Seeder;

class BeachSeeder extends Seeder
{
    public function run(): void
    {
        $beaches = [
            ['name' => 'Sunrise Beach', 'from' => '07:00', 'to' => '20:00', 'village_id' => 1, 'status' => 1],
            ['name' => 'Sunset Beach', 'from' => '08:00', 'to' => '21:00', 'village_id' => 1, 'status' => 1],
        ];

        foreach ($beaches as $beach) {
            Beach::create($beach);
        }
    }
}
