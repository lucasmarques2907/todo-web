<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TodoListFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'title' => fake()->word(),
            'description' => fake()->sentence(6, true),
        ];
    }
}
