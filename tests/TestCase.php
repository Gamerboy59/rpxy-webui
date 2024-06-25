<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();

        // Führen Sie den Test-DatabaseSeeder aus, wenn die Umgebung 'testing' ist
        if (env('APP_ENV') === 'testing' && env('DB_SEEDER')) {
            $this->seed(env('DB_SEEDER'));
        }
    }
}
