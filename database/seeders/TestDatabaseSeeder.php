<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\RustProxy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds and factories.
     */
    public function run()
    {        
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        RustProxy::factory()
            ->count(5)
            ->hasUpstreams(2)
            ->create();
    }
}
