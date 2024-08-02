<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image' => fake()->name(),
            'title' => fake()->name(),
            'link' => fake()->name(),
            'date' => fake()->dateTime()->format('d-m-Y H:i:s'),
            'content' => fake()->name(),
            'status' => fake()->name(),
            'writer' => fake()->randomDigit(),
            'editor' => fake()->randomDigit(),
            'company' => fake()->randomDigit()
        ];
    }
}
