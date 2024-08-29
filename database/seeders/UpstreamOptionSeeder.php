<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UpstreamOption;

class UpstreamOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = ['upgrade_insecure_requests', 'force_http11_upstream', 'force_http2_upstream', 'set_upstream_host', 'keep_original_host'];

        foreach ($options as $option) {
            UpstreamOption::firstOrCreate(['option' => $option]);
        }
    }
}
