<?php

use App\Actions\Spaces\CreateSpace;
use App\Actions\Testimonials\SubmitTestimonial;
use App\Exceptions\PlanLimitReachedException;
use App\Models\Space;
use App\Models\Testimonial;
use App\Models\User;

function newSpaceAttributes(string $slug): array
{
    return ['title' => 'T', 'subtitle' => 'S', 'ask' => 'A', 'slug' => $slug];
}

function newTestimonialAttributes(): array
{
    return ['submitter_name' => 'A', 'submitter_email' => 'a@b.co', 'testimonial_text' => 'Hi'];
}

test('a free user at the space limit cannot create another', function () {
    $user = User::factory()->has(Space::factory()->count(3))->create();

    expect(fn () => app(CreateSpace::class)->handle($user, newSpaceAttributes('fourth')))
        ->toThrow(PlanLimitReachedException::class)
        ->and($user->spaces()->count())->toBe(3);
});

test('a free user under the limit can create a space', function () {
    $user = User::factory()->has(Space::factory()->count(2))->create();

    $space = app(CreateSpace::class)->handle($user, newSpaceAttributes('third'));

    expect($space->exists)->toBeTrue()->and($user->spaces()->count())->toBe(3)
        ->and($space->embedConfiguration)->not->toBeNull();
});

test('a pro user can exceed the free space limit', function () {
    $user = User::factory()->has(Space::factory()->count(3))->create();
    $user->subscriptions()->create(['type' => 'default', 'stripe_id' => 'sub_1', 'stripe_status' => 'active', 'stripe_price' => 'p', 'quantity' => 1]);

    app(CreateSpace::class)->handle($user, newSpaceAttributes('fourth'));

    expect($user->spaces()->count())->toBe(4);
});

test('a full space refuses new testimonials with a friendly message', function () {
    $space = Space::factory()->create();
    Testimonial::factory()->count(100)->for($space)->create();

    expect(fn () => app(SubmitTestimonial::class)->handle($space, newTestimonialAttributes()))
        ->toThrow(PlanLimitReachedException::class, 'not accepting new testimonials')
        ->and($space->testimonials()->count())->toBe(100);
});

test('a space under its limit accepts a testimonial', function () {
    $space = Space::factory()->create();

    $testimonial = app(SubmitTestimonial::class)->handle($space, newTestimonialAttributes());

    expect($testimonial->space_id)->toBe($space->id);
});

test('a downgraded user keeps everything but cannot add more', function () {
    $user = User::factory()->has(Space::factory()->count(5))->create();

    expect(fn () => app(CreateSpace::class)->handle($user, newSpaceAttributes('sixth')))
        ->toThrow(PlanLimitReachedException::class)
        ->and($user->spaces()->count())->toBe(5);
});
