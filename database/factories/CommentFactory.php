<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'     => fake()->name(),
            'email'    => fake()->safeEmail(),
            'comment'  => fake()->paragraph(),
            'post_id'  => Post::factory(),
        ];
    }
}
