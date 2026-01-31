<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ShortUrlFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            },
            'company_id' => function (array $attributes) {
                return \App\Models\User::find($attributes['user_id'])->company_id;
            },
            'title' => $this->faker->sentence(3),
            'original_url' => $this->faker->url(),
            'short_code' => $this->faker->unique()->regexify('[A-Za-z0-9]{6}'),
            'clicks' => $this->faker->numberBetween(0, 1000),
            'is_active' => true,
            'expires_at' => $this->faker->optional()->dateTimeBetween('+1 week', '+1 year'),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
        ]);
    }
}