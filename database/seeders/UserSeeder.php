<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'phone' => '+201000000001',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'status' => 1,
            'gender' => 'male',
        ]);

        // Village Admin
        User::create([
            'name' => 'Village Admin',
            'email' => 'village@gmail.com',
            'phone' => '+201000000002',
            'password' => Hash::make('password'),
            'role' => 'village',
            'village_id' => 1,
            'status' => 1,
            'gender' => 'male',
        ]);

        // Regular User
        User::create([
            'name' => 'Ahmed Mohamed',
            'email' => 'user@gmail.com',
            'phone' => '+201000000003',
            'password' => Hash::make('password'),
            'role' => 'user',
            'village_id' => 1,
            'status' => 1,
            'gender' => 'male',
        ]);

        // Provider Admin
        User::create([
            'name' => 'Provider Admin',
            'email' => 'provider@sea-go.org',
            'phone' => '+201000000004',
            'password' => Hash::make('password'),
            'role' => 'provider',
            'provider_id' => 1,
            'status' => 1,
            'gender' => 'male',
        ]);
    }
}
