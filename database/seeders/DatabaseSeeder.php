<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Independent tables first (no foreign keys)
            ZoneSeeder::class,
            ServiceTypeSeeder::class,
            MaintenanceTypeSeeder::class,
            AppartmentTypeSeeder::class,
            PaymentMethodSeeder::class,
            AdminPositionSeeder::class,
            SettingSeeder::class,

            // Packages (depends on service_type, maintenance_type)
            PackageSeeder::class,

            // Mall (depends on zone)
            MallSeeder::class,

            // Village (depends on package, zone)
            VillageSeeder::class,

            // Providers (depends on village, package, service_type)
            ProviderSeeder::class,
            ServiceProviderSeeder::class,

            // Village infrastructure (depends on village)
            GateSeeder::class,
            BeachSeeder::class,
            PoolSeeder::class,
            SecurityManSeeder::class,

            // Users (depends on village, provider, service_provider)
            UserSeeder::class,

            // Appartments (depends on village, user, appartment_type)
            AppartmentSeeder::class,
        ]);
    }
}
