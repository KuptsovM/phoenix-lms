<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['draft', 'published', 'archived']);

        return [
            'title' => fake()->sentence(4),
            'slug' => fake()->unique()->slug(3),
            'description' => fake()->paragraphs(2, true),
            'status' => $status,
            'is_featured' => fake()->boolean(20),
            'published_at' => $status === 'published'
                ? fake()->dateTimeBetween('-3 months', 'now')
                : null,
            'author_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
        ];
    }
}
