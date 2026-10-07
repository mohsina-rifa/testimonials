<?php

namespace Database\Factories;

use App\Enums\Theme;
use App\Models\Space;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Space>
 */
class SpaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->company();

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'subtitle' => fake()->sentence(),
            'ask' => fake()->sentence().'?',
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('####'),
            'theme' => Theme::Light,
            'rating_enabled' => true,
            'field_configuration' => [],
        ];
    }
}
