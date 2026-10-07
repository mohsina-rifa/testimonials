<?php

use App\Actions\Analytics\ComputeOwnerAnalytics;
use App\Models\Space;
use App\Models\Testimonial;
use App\Models\User;

beforeEach(function () {
    config(['app.timezone' => 'UTC']);
    $this->travelTo(now()->setTime(12, 0));
});

test('totals and unique submitters follow the sample data', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    [$product, $consulting] = Space::factory()->count(2)->for($alice)->create();
    $course = Space::factory()->for($bob)->create();

    Testimonial::factory()->for($product)->onWallOfLove()->create(['submitter_email' => 'dana@gmail.com']);
    Testimonial::factory()->for($product)->create(['submitter_email' => 'eli@initech.com']);
    Testimonial::factory()->for($consulting)->onWallOfLove()->create(['submitter_email' => 'dana@gmail.com']);
    Testimonial::factory()->for($course)->create(['submitter_email' => 'dana@gmail.com']);

    $result = app(ComputeOwnerAnalytics::class)->handle($alice);

    expect($result)
        ->total_spaces->toBe(2)
        ->total_testimonials->toBe(3)
        ->unique_submitters->toBe(2)
        ->wall_of_love_count->toBe(2)
        ->collected_in_period->toBe(3);
});

test('the period filter limits collected testimonials', function () {
    $owner = User::factory()->create();
    $space = Space::factory()->for($owner)->create();
    Testimonial::factory()->for($space)->create(['created_at' => now()->subDays(3)]);
    Testimonial::factory()->for($space)->create(['created_at' => now()->subDays(40)]);

    $analytics = app(ComputeOwnerAnalytics::class);

    expect($analytics->handle($owner, 7)['collected_in_period'])->toBe(1)
        ->and($analytics->handle($owner, 30)['collected_in_period'])->toBe(1)
        ->and($analytics->handle($owner, 90)['collected_in_period'])->toBe(2)
        ->and($analytics->handle($owner)['collected_in_period'])->toBe(2);
});

test('the daily series is zero filled', function () {
    $owner = User::factory()->create();
    $space = Space::factory()->for($owner)->create();
    Testimonial::factory()->for($space)->create(['created_at' => now()->subDays(2)]);
    Testimonial::factory()->count(2)->for($space)->create(['created_at' => now()]);

    $daily = app(ComputeOwnerAnalytics::class)->handle($owner, 3)['daily'];

    expect(array_values($daily))->toBe([1, 0, 2])
        ->and(array_key_first($daily))->toBe(now()->subDays(2)->toDateString());
});

test('daily buckets use the app timezone', function () {
    config(['app.timezone' => 'Asia/Dhaka']);
    $this->travelTo(now('Asia/Dhaka')->setTime(12, 0));
    $owner = User::factory()->create();
    $space = Space::factory()->for($owner)->create();
    Testimonial::factory()->for($space)->create(['created_at' => now('Asia/Dhaka')->setTime(1, 0)->utc()]);

    $daily = app(ComputeOwnerAnalytics::class)->handle($owner, 2)['daily'];

    expect($daily[now('Asia/Dhaka')->toDateString()])->toBe(1);
});

test('a new testimonial appears on the next read', function () {
    $owner = User::factory()->create();
    $space = Space::factory()->for($owner)->create();
    $analytics = app(ComputeOwnerAnalytics::class);

    expect($analytics->handle($owner)['total_testimonials'])->toBe(0);

    Testimonial::factory()->for($space)->create();

    expect($analytics->handle($owner)['total_testimonials'])->toBe(1);
});
