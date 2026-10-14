<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'sku' => fake()->unique()->bothify('PRD-####-???'),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 10, 2500),
            'stock' => fake()->numberBetween(0, 250),
            'is_active' => fake()->boolean(90),
            'published_at' => fake()->optional(0.9)->dateTimeBetween('-18 months', 'now'),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }

    public function unpublished(): static
    {
        return $this->state(fn (): array => [
            'published_at' => null,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (): array => [
            'stock' => 0,
        ]);
    }
}
