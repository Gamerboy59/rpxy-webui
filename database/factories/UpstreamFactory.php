<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RustProxy;
use App\Models\LoadbalanceType;
use App\Models\Upstream;
use App\Models\UpstreamLocation;
use App\Models\UpstreamOption;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Upstream>
 */
class UpstreamFactory extends Factory
{

    protected $model = \App\Models\Upstream::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rust_proxy_id' => RustProxy::factory(),
            'tls' => fake()->boolean(),
            'loadbalance_type_id' => LoadbalanceType::inRandomOrder()->first()->id
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Upstream $upstream) {
            $optionIds = UpstreamOption::inRandomOrder()->take(2)->pluck('id')->toArray();
            $upstream->upstreamOptions()->attach($optionIds);

            UpstreamLocation::factory()->count(rand(2, 5))->create(['upstream_id' => $upstream->id]);
        });
    }
}
