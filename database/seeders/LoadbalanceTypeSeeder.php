<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LoadbalanceType;

class LoadbalanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['none', 'round_robin', 'random', 'sticky'];

        foreach ($types as $type) {
            LoadbalanceType::create(['name' => $type]);
        }
    }
}
