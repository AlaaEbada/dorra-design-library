<?php

namespace Database\Factories;

use App\Enums\DesignStatus;
use App\Models\Category;
use App\Models\Design;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Design> */
class DesignFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'category_id' => Category::factory(),
            'status' => DesignStatus::Draft,
            'thumbnail' => 'images/design-placeholder.svg',
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => DesignStatus::Published]);
    }
}
