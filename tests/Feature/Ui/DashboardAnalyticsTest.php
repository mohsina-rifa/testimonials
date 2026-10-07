<?php

use App\Models\Space;
use App\Models\Testimonial;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('dashboard shows only the owner totals', function () {
    $user = User::factory()->create();
    $space = Space::factory()->for($user)->create();
    Testimonial::factory()->for($space)->count(2)->create();
    Testimonial::factory()->for($space)->onWallOfLove()->create();
    Testimonial::factory()->for(Space::factory())->count(4)->create();

    $this->actingAs($user)->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->where('period', 'all')
            ->where('analytics.total_spaces', 1)
            ->where('analytics.total_testimonials', 3)
            ->where('analytics.wall_of_love_count', 1)
            ->where('plan.plan', 'free'));
});

test('period filter produces a zero filled series', function () {
    $user = User::factory()->create();
    $space = Space::factory()->for($user)->create();
    Testimonial::factory()->for($space)->create();
    Testimonial::factory()->for($space)->create(['created_at' => now()->subDays(40)]);

    $this->actingAs($user)->get(route('dashboard', ['period' => '7']))
        ->assertInertia(fn (Assert $page) => $page
            ->where('period', '7')
            ->where('analytics.collected_in_period', 1)
            ->has('analytics.daily', 7));

    $this->actingAs($user)->get(route('dashboard', ['period' => 'bogus']))
        ->assertInertia(fn (Assert $page) => $page->where('period', 'all')->where('analytics.collected_in_period', 2));
});

test('new owner gets an empty analytics payload', function () {
    $this->actingAs(User::factory()->create())->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('analytics.total_spaces', 0)->has('analytics.daily', 0));
});
