<?php

namespace Database\Seeders;

use App\Models\SecurityMan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SecurityManSeeder extends Seeder
{
    public function run(): void
    {
        $security = [
            [
                'name' => 'Omar Hassan',
                'email' => 'security1@sea-go.org',
                'phone' => '+201300000001',
                'password' => Hash::make('password'),
                'village_id' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Khaled Ali',
                'email' => 'security2@sea-go.org',
                'phone' => '+201300000002',
                'password' => Hash::make('password'),
                'village_id' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Mohamed Samir',
                'email' => 'security3@sea-go.org',
                'phone' => '+201300000003',
                'password' => Hash::make('password'),
                'village_id' => 2,
                'status' => 1,
            ],
        ];

        foreach ($security as $man) {
            SecurityMan::create($man);
        }
    }
}
