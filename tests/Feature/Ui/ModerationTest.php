<?php

use App\Models\Space;
use App\Models\Testimonial;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('owner lists and filters testimonials', function () {
    $user = User::factory()->create();
    $space = Space::factory()->for($user)->create();
    Testimonial::factory()->for($space)->count(2)->create();
    Testimonial::factory()->for($space)->favorite()->create();

    $this->actingAs($user)->get(route('spaces.testimonials.index', [$space, 'filter' => 'favorites']))
        ->assertInertia(fn (Assert $page) => $page
            ->component('spaces/testimonials')
            ->where('filter', 'favorites')
            ->has('testimonials.data', 1));
});

test('owner toggles favorite and hidden', function () {
    $user = User::factory()->create();
    $space = Space::factory()->for($user)->create();
    $testimonial = Testimonial::factory()->for($space)->consented()->create();

    $this->actingAs($user)->patch(route('spaces.testimonials.update', [$space, $testimonial]), ['is_favorite' => true, 'is_hidden' => true]);

    expect($testimonial->fresh()->is_favorite)->toBeTrue()
        ->and($testimonial->fresh()->is_hidden)->toBeTrue();
});

test('wall of love requires consent', function () {
    $user = User::factory()->create();
    $space = Space::factory()->for($user)->create();
    $withoutConsent = Testimonial::factory()->for($space)->create();
    $withConsent = Testimonial::factory()->for($space)->consented()->create();

    $this->actingAs($user)->patch(route('spaces.testimonials.update', [$space, $withoutConsent]), ['is_wall_of_love' => true])
        ->assertSessionHasErrors('is_wall_of_love');
    $this->actingAs($user)->patch(route('spaces.testimonials.update', [$space, $withConsent]), ['is_wall_of_love' => true]);

    expect($withoutConsent->fresh()->is_wall_of_love)->toBeFalse()
        ->and($withConsent->fresh()->is_wall_of_love)->toBeTrue();
});

test('owner deletes a testimonial', function () {
    $user = User::factory()->create();
    $space = Space::factory()->for($user)->create();
    $testimonial = Testimonial::factory()->for($space)->create();

    $this->actingAs($user)->delete(route('spaces.testimonials.destroy', [$space, $testimonial]));

    expect(Testimonial::count())->toBe(0);
});

test('other users cannot moderate', function () {
    $space = Space::factory()->create();
    $testimonial = Testimonial::factory()->for($space)->consented()->create();
    $intruder = User::factory()->create();

    $this->actingAs($intruder)->get(route('spaces.testimonials.index', $space))->assertForbidden();
    $this->actingAs($intruder)->patch(route('spaces.testimonials.update', [$space, $testimonial]), ['is_favorite' => true])->assertForbidden();
    $this->actingAs($intruder)->delete(route('spaces.testimonials.destroy', [$space, $testimonial]))->assertForbidden();

    expect($testimonial->fresh()->is_favorite)->toBeFalse();
});
