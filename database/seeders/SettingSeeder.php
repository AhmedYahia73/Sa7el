<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['name' => 'app_name', 'value' => 'Sea Go'],
            ['name' => 'app_email', 'value' => 'info@sea-go.org'],
            ['name' => 'app_phone', 'value' => '+20100000000'],
            ['name' => 'app_address', 'value' => 'Egypt'],
            ['name' => 'app_version', 'value' => '1.0.0'],
            ['name' => 'visitor_limit', 'value' => '5'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
