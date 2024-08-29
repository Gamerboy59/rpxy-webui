<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'single_user_mode',
                'value' => true,
                'type' => 'checkbox'
            ],
        ];
        
        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']], // Search criteria
                ['value' => $setting['value'], 'type' => $setting['type']] // Updateable value or create new dataset of them including key
            );
        }
    }
}
