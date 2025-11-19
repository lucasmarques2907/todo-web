<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TodoItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'list_id' => 1,
            'title' => fake()->word(),
            'description' => fake()->sentence(6, true),
        ];
    }
}
