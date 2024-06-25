<?php

namespace Database\Factories;

use App\Models\Upstream;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UpstreamLocation>
 */
class UpstreamLocationFactory extends Factory
{

    protected $model = \App\Models\UpstreamLocation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'upstream_id' => Upstream::factory(),
            'location' => fake()->domainName(),
            'tls' => fake()->boolean()
        ];
    }
}
