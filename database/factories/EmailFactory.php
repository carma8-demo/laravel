<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use function fake;

/** @extends Factory<Email> */
final class EmailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => fake()->unique(maxRetries: 100_000)->email(),
            'checked' => fake()->boolean,
            'valid' => static fn (array $attributes): bool => $attributes['checked'] && fake()->boolean,
        ];
    }
}
