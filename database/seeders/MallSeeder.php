<?php

namespace Database\Seeders;

use App\Models\Mall;
use Illuminate\Database\Seeder;

class MallSeeder extends Seeder
{
    public function run(): void
    {
        $malls = [
            [
                'name' => 'Sea Mall',
                'description' => 'Main shopping mall by the sea',
                'location' => 'North Coast',
                'open_from' => '09:00',
                'open_to' => '23:00',
                'zone_id' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Sunset Plaza',
                'description' => 'Commercial plaza with various shops',
                'location' => 'South Zone',
                'open_from' => '10:00',
                'open_to' => '22:00',
                'zone_id' => 2,
                'status' => 1,
            ],
        ];

        foreach ($malls as $mall) {
            Mall::create($mall);
        }
    }
}
