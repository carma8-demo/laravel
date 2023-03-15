<?php

declare(strict_types=1);

namespace Database\Factories;

use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use function fake;

/** @extends Factory<User> */
final class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username' => fake()->unique(maxRetries: 100_000)->userName(),
            'confirmed' => fake()->boolean,
            'validts' => fake()->dateTimeBetween('-6 months', '+6 months'),
            'notifiedts' => static fn (array $attributes): ?DateTime => fake()->optional()->dateTimeBetween('-12 months', $attributes['validts']),
        ];
    }
}
