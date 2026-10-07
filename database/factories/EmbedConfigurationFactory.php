<?php

namespace Database\Factories;

use App\Enums\EmbedLayout;
use App\Models\EmbedConfiguration;
use App\Models\Space;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmbedConfiguration>
 */
class EmbedConfigurationFactory extends Factory
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
            'layout' => EmbedLayout::Masonry,
            'dark_mode' => false,
            'animation_enabled' => true,
            'background_color' => '#ffffff',
            'show_rating' => true,
            'show_company' => true,
            'show_profile_photo' => true,
        ];
    }
}
