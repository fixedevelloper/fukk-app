<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'title'   => $title,
            'slug'    => Str::slug($title) . '-' . fake()->unique()->randomNumber(),
            'content' => fake()->paragraphs(6, true),
            'user_id' => User::factory(),   // génère automatiquement un auteur
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($post) {

            // Ajouter 1 à 3 catégories
            $categories = \App\Models\Category::inRandomOrder()
                ->take(rand(1, 3))
                ->pluck('id');

            $post->categories()->attach($categories);

            // Ajouter 1 à 5 tags
            $tags = \App\Models\Tag::inRandomOrder()
                ->take(rand(1, 5))
                ->pluck('id');

            $post->tags()->attach($tags);

            // Ajouter une fausse image (Spatie)
            // IMPORTANT : simuler une image réelle
            $post->addMediaFromUrl('https://picsum.photos/800/600')
                ->toMediaCollection('images');

            // Ajouter 0 à 5 commentaires
            \App\Models\Comment::factory(rand(0, 5))->create([
                'post_id' => $post->id,
            ]);
        });
    }

}
