<?php

use App\Models\Space;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function validSpacePayload(array $overrides = []): array
{
    return [
        'title' => 'Acme',
        'subtitle' => 'Tell us',
        'ask' => 'What do you think?',
        'slug' => 'acme',
        'theme' => 'light',
        'rating_enabled' => true,
        'field_configuration' => [
            'company' => ['enabled' => true, 'required' => false],
            'social_link' => ['enabled' => false, 'required' => false],
            'profile_photo' => ['enabled' => false, 'required' => false],
        ],
        ...$overrides,
    ];
}

test('guests cannot reach spaces', function () {
    $this->get(route('spaces.index'))->assertRedirect(route('login'));
});

test('index lists only the owner spaces', function () {
    $user = User::factory()->create();
    Space::factory()->for($user)->count(2)->create();
    Space::factory()->count(2)->create();

    $this->actingAs($user)->get(route('spaces.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('spaces/index')
            ->has('spaces', 2)
            ->where('plan.max_spaces', 3));
});

test('owner creates a space', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('spaces.store'), validSpacePayload())
        ->assertRedirect();

    expect($user->spaces()->where('slug', 'acme')->exists())->toBeTrue();
});

test('duplicate slug is rejected', function () {
    $user = User::factory()->create();
    Space::factory()->create(['slug' => 'acme']);

    $this->actingAs($user)->post(route('spaces.store'), validSpacePayload())
        ->assertSessionHasErrors('slug');

    expect($user->spaces()->count())->toBe(0);
});

test('free owner at the limit is refused with a message', function () {
    $user = User::factory()->create();
    Space::factory()->for($user)->count(3)->create();

    $this->actingAs($user)->post(route('spaces.store'), validSpacePayload())
        ->assertSessionHas('error');

    expect($user->spaces()->count())->toBe(3);
});

test('slug change keeps the public id', function () {
    $user = User::factory()->create();
    $space = Space::factory()->for($user)->create();
    $publicId = $space->public_id;

    $this->actingAs($user)->put(route('spaces.update', $space), validSpacePayload(['slug' => 'renamed']))
        ->assertSessionHasNoErrors();

    expect($space->fresh()->slug)->toBe('renamed')
        ->and($space->fresh()->public_id)->toBe($publicId);
});

test('owner deletes a space with its testimonials', function () {
    $user = User::factory()->create();
    $space = Space::factory()->for($user)->hasTestimonials(2)->create();

    $this->actingAs($user)->delete(route('spaces.destroy', $space))->assertRedirect(route('spaces.index'));

    expect(Space::count())->toBe(0);
});

test('other users cannot manage a space', function () {
    $space = Space::factory()->create();
    $intruder = User::factory()->create();

    $this->actingAs($intruder)->get(route('spaces.edit', $space))->assertForbidden();
    $this->actingAs($intruder)->put(route('spaces.update', $space), validSpacePayload())->assertForbidden();
    $this->actingAs($intruder)->delete(route('spaces.destroy', $space))->assertForbidden();
});

test('flash messages are shared with pages', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->withSession(['success' => 'Done'])->get(route('spaces.index'))
        ->assertInertia(fn (Assert $page) => $page->where('flash.success', 'Done'));
});
