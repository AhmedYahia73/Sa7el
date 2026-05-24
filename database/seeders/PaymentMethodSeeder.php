<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['name' => 'Cash', 'description' => 'Pay with cash on delivery', 'status' => 1],
            ['name' => 'Credit Card', 'description' => 'Pay with Visa or Mastercard', 'status' => 1],
            ['name' => 'Bank Transfer', 'description' => 'Direct bank transfer', 'status' => 1],
            ['name' => 'Wallet', 'description' => 'Pay using digital wallet', 'status' => 1],
        ];

        foreach ($methods as $method) {
            PaymentMethod::create($method);
        }
    }
}
