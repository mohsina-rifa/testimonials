<?php

use App\Models\Space;
use App\Models\Testimonial;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function embedPayload(array $overrides = []): array
{
    return [
        'layout' => 'carousel',
        'dark_mode' => true,
        'animation_enabled' => false,
        'background_color' => '#112233',
        'show_rating' => true,
        'show_company' => false,
        'show_profile_photo' => true,
        ...$overrides,
    ];
}

test('owner saves embed settings', function () {
    $user = User::factory()->create();
    $space = Space::factory()->for($user)->create();

    $this->actingAs($user)->put(route('spaces.embed.update', $space), embedPayload())->assertSessionHasNoErrors();

    expect($space->embedConfiguration->fresh()->layout->value)->toBe('carousel')
        ->and($space->embedConfiguration->fresh()->background_color)->toBe('#112233');
});

test('invalid embed settings are rejected', function () {
    $user = User::factory()->create();
    $space = Space::factory()->for($user)->create();

    $this->actingAs($user)->put(route('spaces.embed.update', $space), embedPayload(['background_color' => 'red', 'layout' => 'grid']))
        ->assertSessionHasErrors(['background_color', 'layout']);
});

test('other users cannot edit embed settings', function () {
    $space = Space::factory()->create();

    $this->actingAs(User::factory()->create())->get(route('spaces.embed.edit', $space))->assertForbidden();
    $this->actingAs(User::factory()->create())->put(route('spaces.embed.update', $space), embedPayload())->assertForbidden();
});

test('public embed shows only visible testimonials without emails', function () {
    $space = Space::factory()->create();
    Testimonial::factory()->for($space)->onWallOfLove()->create(['submitter_email' => 'secret@example.com']);
    Testimonial::factory()->for($space)->onWallOfLove()->hidden()->create();
    Testimonial::factory()->for($space)->consented()->create();
    Testimonial::factory()->for($space)->create();

    $response = $this->get(route('embed.show', $space->public_id));

    $response->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('embed/show')
        ->has('testimonials', 1)
        ->missing('testimonials.0.submitter_email')
        ->missing('testimonials.0.email'));

    expect($response->getContent())->not->toContain('secret@example.com');
});

test('embed for an unknown id 404s and the empty wall renders', function () {
    $this->get(route('embed.show', 'nope'))->assertNotFound();

    $space = Space::factory()->create();
    $this->get(route('embed.show', $space->public_id))
        ->assertInertia(fn (Assert $page) => $page->has('testimonials', 0));
});
