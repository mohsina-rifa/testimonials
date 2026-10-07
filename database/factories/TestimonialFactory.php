<?php

namespace Database\Factories;

use App\Models\Space;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'space_id' => Space::factory(),
            'submitter_name' => fake()->name(),
            'submitter_email' => fake()->safeEmail(),
            'company_name' => null,
            'social_link' => null,
            'profile_photo_path' => null,
            'testimonial_text' => fake()->paragraph(),
            'rating' => null,
            'consent_given' => false,
            'is_favorite' => false,
            'is_wall_of_love' => false,
            'is_hidden' => false,
        ];
    }

    public function consented(): static
    {
        return $this->state(['consent_given' => true]);
    }

    public function favorite(): static
    {
        return $this->state(['is_favorite' => true]);
    }

    public function onWallOfLove(): static
    {
        return $this->state(['consent_given' => true, 'is_wall_of_love' => true]);
    }

    public function hidden(): static
    {
        return $this->state(['is_hidden' => true]);
    }
}
