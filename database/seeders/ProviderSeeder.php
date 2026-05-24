<?php

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            [
                'name' => 'Sea Restaurant',
                'phone' => '+201100000001',
                'service_id' => 1,
                'village_id' => 1,
                'package_id' => 3,
                'description' => 'Fresh seafood restaurant',
                'location' => 'Blue Bay Village',
                'open_from' => '10:00',
                'open_to' => '23:00',
                'status' => 1,
            ],
            [
                'name' => 'Green Pharmacy',
                'phone' => '+201100000002',
                'service_id' => 2,
                'village_id' => 1,
                'package_id' => 3,
                'description' => '24/7 pharmacy service',
                'location' => 'Blue Bay Village',
                'open_from' => '00:00',
                'open_to' => '23:59',
                'status' => 1,
            ],
            [
                'name' => 'FitLife Gym',
                'phone' => '+201100000003',
                'service_id' => 4,
                'village_id' => 2,
                'package_id' => 4,
                'description' => 'Modern gym with all equipment',
                'location' => 'Green Hills Village',
                'open_from' => '06:00',
                'open_to' => '22:00',
                'status' => 1,
            ],
        ];

        foreach ($providers as $provider) {
            Provider::create($provider);
        }
    }
}
