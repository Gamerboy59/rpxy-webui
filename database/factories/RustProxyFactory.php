<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RustProxy>
 */
class RustProxyFactory extends Factory
{

    protected $model = \App\Models\RustProxy::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'app_name' => fake()->word(),
            'server_name' => fake()->domainName(),
            'ssl_enabled' => fake()->boolean(),
            'https_redirection' => fake()->boolean(),
            'letsencrypt_enabled' => fake()->boolean(),
            'tls_cert_path' => fake()->filePath(),
            'tls_cert_key_path' => fake()->filePath(),
            'client_ca_cert_path' => fake()->filePath()
        ];
    }
}
