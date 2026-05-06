<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'external_id' => fake()->unique()->numberBetween(1, 999999),
            'title'       => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'type'        => fake()->randomElement(['anime', 'movie', 'series']),
            'source'      => fake()->randomElement(['Manga', 'Visual novel', 'Original', null]),
            'status'      => fake()->randomElement(['Finished Airing', 'Currently Airing', 'Not yet aired']),
            'airing'      => false,
            'approved'    => true,
            'episodes'    => fake()->optional()->numberBetween(1, 150),
            'duration'    => fake()->randomElement(['24 min per ep', '23 min per ep', '1 hr 30 min']),
            'score'       => fake()->optional()->randomFloat(2, 1, 10),
            'scored_by'   => fake()->optional()->numberBetween(100, 2000000),
            'rank'        => fake()->optional()->numberBetween(1, 10000),
            'popularity'  => fake()->optional()->numberBetween(1, 10000),
            'members'     => fake()->optional()->numberBetween(100, 3000000),
            'favorites'   => fake()->optional()->numberBetween(0, 200000),
            'year'        => fake()->optional()->numberBetween(1980, 2025),
            'images'      => [
                'jpg' => [
                    'image_url'       => 'https://placehold.co/300x400.jpg',
                    'small_image_url' => 'https://placehold.co/150x200.jpg',
                    'large_image_url' => 'https://placehold.co/600x800.jpg',
                ],
            ],
            'studios' => [['name' => fake()->company()]],
            'genres'  => [['name' => fake()->randomElement([
                'Action', 'Drama', 'Comedy', 'Fantasy', 'Sci-Fi', 'Romance', 'Thriller'
            ])]],
        ];
    }

    public function anime(): static
    {
        return $this->state(fn () => ['type' => 'anime']);
    }

    public function movie(): static
    {
        return $this->state(fn () => [
            'type'     => 'movie',
            'episodes' => 1,
            'airing'   => false,
            'status'   => 'Finished Airing',
        ]);
    }

    public function airing(): static
    {
        return $this->state(fn () => [
            'airing' => true,
            'status' => 'Currently Airing',
        ]);
    }

    public function unapproved(): static
    {
        return $this->state(fn () => ['approved' => false]);
    }
}