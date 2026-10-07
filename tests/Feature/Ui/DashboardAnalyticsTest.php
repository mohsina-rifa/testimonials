<?php

use App\Models\Space;
use App\Models\Testimonial;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('space dashboard shows only that space totals', function () {
    $user = User::factory()->create();
    $space = Space::factory()->for($user)->create();
    Testimonial::factory()->for($space)->count(2)->create();
    Testimonial::factory()->for($space)->onWallOfLove()->create();
    Testimonial::factory()->for(Space::factory()->for($user))->count(5)->create();
    Testimonial::factory()->for(Space::factory())->count(4)->create();

    $this->actingAs($user)->get(route('spaces.dashboard', $space))
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->where('space.id', $space->id)
            ->where('period', 'all')
            ->where('analytics.total_testimonials', 3)
            ->where('analytics.wall_of_love_count', 1));
});

test('period filter produces a zero filled series', function () {
    $user = User::factory()->create();
    $space = Space::factory()->for($user)->create();
    Testimonial::factory()->for($space)->create();
    Testimonial::factory()->for($space)->create(['created_at' => now()->subDays(40)]);

    $this->actingAs($user)->get(route('spaces.dashboard', [$space, 'period' => '7']))
        ->assertInertia(fn (Assert $page) => $page
            ->where('period', '7')
            ->where('analytics.collected_in_period', 1)
            ->has('analytics.daily', 7));

    $this->actingAs($user)->get(route('spaces.dashboard', [$space, 'period' => 'bogus']))
        ->assertInertia(fn (Assert $page) => $page->where('period', 'all')->where('analytics.collected_in_period', 2));
});

test('empty space gets an empty series', function () {
    $user = User::factory()->create();
    $space = Space::factory()->for($user)->create();

    $this->actingAs($user)->get(route('spaces.dashboard', $space))
        ->assertInertia(fn (Assert $page) => $page->where('analytics.total_testimonials', 0)->has('analytics.daily', 0));
});

test('another owner cannot open the dashboard and guests are redirected', function () {
    $space = Space::factory()->create();

    $this->actingAs(User::factory()->create())->get(route('spaces.dashboard', $space))->assertForbidden();

    auth()->logout();
    $this->get(route('spaces.dashboard', $space))->assertRedirect(route('login'));
});

test('the central dashboard no longer exists', function () {
    $this->actingAs(User::factory()->create())->get('/dashboard')->assertNotFound();
});

test('only the owner spaces are shared with every page', function () {
    $user = User::factory()->create();
    $mine = Space::factory()->for($user)->create();
    Space::factory()->create();

    $this->actingAs($user)->get(route('spaces.index'))
        ->assertInertia(fn (Assert $page) => $page->has('ownerSpaces', 1)->where('ownerSpaces.0.id', $mine->id));
});

test('the spaces index shows plan usage', function () {
    $user = User::factory()->create();
    Space::factory()->for($user)->count(2)->create();

    $this->actingAs($user)->get(route('spaces.index'))
        ->assertInertia(fn (Assert $page) => $page->where('plan.spaces_used', 2)->where('plan.max_spaces', 3));
});

test('guests are given no spaces', function () {
    $this->get(route('home'))->assertInertia(fn (Assert $page) => $page->where('ownerSpaces', []));
});
