<?php

namespace Database\Seeders;

use App\Models\ServiceProvider;
use Illuminate\Database\Seeder;

class ServiceProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            [
                'name' => 'Quick Fix Plumbing',
                'phone' => '+201200000001',
                'maintenance_type_id' => 1,
                'village_id' => 1,
                'package_id' => 5,
                'description' => 'Professional plumbing services',
                'location' => 'Blue Bay Village',
                'open_from' => '08:00',
                'open_to' => '20:00',
                'status' => 1,
            ],
            [
                'name' => 'PowerTech Electrical',
                'phone' => '+201200000002',
                'maintenance_type_id' => 2,
                'village_id' => 1,
                'package_id' => 5,
                'description' => 'Certified electrical maintenance',
                'location' => 'Blue Bay Village',
                'open_from' => '08:00',
                'open_to' => '20:00',
                'status' => 1,
            ],
            [
                'name' => 'CoolAir AC Services',
                'phone' => '+201200000003',
                'maintenance_type_id' => 3,
                'village_id' => 2,
                'package_id' => 5,
                'description' => 'AC installation and repair',
                'location' => 'Green Hills Village',
                'open_from' => '09:00',
                'open_to' => '18:00',
                'status' => 1,
            ],
        ];

        foreach ($providers as $provider) {
            ServiceProvider::create($provider);
        }
    }
}
