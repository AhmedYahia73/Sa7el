<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Village Basic',
                'description' => 'Basic package for small villages',
                'price' => 5000,
                'type' => 'village',
                'feez' => 10,
                'discount' => 5,
                'admin_num' => 2,
                'security_num' => 5,
                'units_num' => 50,
                'maintenance_module' => 1,
                'beach_pool_module' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Village Premium',
                'description' => 'Premium package for large villages',
                'price' => 15000,
                'type' => 'village',
                'feez' => 8,
                'discount' => 10,
                'admin_num' => 5,
                'security_num' => 10,
                'units_num' => 200,
                'maintenance_module' => 1,
                'beach_pool_module' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Provider Starter',
                'description' => 'Starter package for service providers',
                'price' => 2000,
                'type' => 'provider',
                'feez' => 15,
                'discount' => 0,
                'admin_num' => 1,
                'security_num' => 0,
                'units_num' => 1,
                'maintenance_module' => 0,
                'beach_pool_module' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Provider Gold',
                'description' => 'Gold package for service providers',
                'price' => 6000,
                'type' => 'provider',
                'feez' => 10,
                'discount' => 5,
                'admin_num' => 3,
                'security_num' => 0,
                'units_num' => 1,
                'maintenance_module' => 0,
                'beach_pool_module' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Maintenance Basic',
                'description' => 'Basic package for maintenance providers',
                'price' => 3000,
                'type' => 'maintenance_provider',
                'feez' => 12,
                'discount' => 0,
                'admin_num' => 1,
                'security_num' => 0,
                'units_num' => 1,
                'maintenance_module' => 1,
                'beach_pool_module' => 0,
                'status' => 1,
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
