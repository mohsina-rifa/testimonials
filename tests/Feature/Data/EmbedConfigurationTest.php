<?php

use App\Enums\EmbedLayout;
use App\Models\EmbedConfiguration;
use App\Models\Space;
use Illuminate\Database\UniqueConstraintViolationException;

test('a new space gets an embed configuration with defaults', function () {
    $configuration = Space::factory()->create()->embedConfiguration()->first();

    expect($configuration)
        ->layout->toBe(EmbedLayout::Masonry)
        ->dark_mode->toBeFalse()
        ->animation_enabled->toBeTrue()
        ->background_color->toBe('#ffffff')
        ->show_rating->toBeTrue()
        ->show_company->toBeTrue()
        ->show_profile_photo->toBeTrue();
});

test('a space cannot have a second embed configuration', function () {
    $space = Space::factory()->create();

    EmbedConfiguration::factory()->create(['space_id' => $space->id]);
})->throws(UniqueConstraintViolationException::class);

test('an unsupported layout is rejected', function () {
    $space = Space::factory()->create();

    $space->embedConfiguration->update(['layout' => 'grid']);
})->throws(ValueError::class);
