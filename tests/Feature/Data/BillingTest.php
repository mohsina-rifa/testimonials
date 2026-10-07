<?php

use App\Enums\Plan;
use App\Models\StripeWebhookEvent;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Schema;

test('users table has billing identifiers but no plan column', function () {
    expect(Schema::hasColumns('users', ['stripe_id', 'pm_type', 'pm_last_four', 'trial_ends_at']))->toBeTrue()
        ->and(Schema::hasColumn('users', 'plan'))->toBeFalse();
});

test('a webhook event is recorded with its id, type and time', function () {
    $event = StripeWebhookEvent::factory()->create(['stripe_event_id' => 'evt_1', 'event_type' => 'invoice.paid']);

    expect($event->fresh())
        ->stripe_event_id->toBe('evt_1')
        ->event_type->toBe('invoice.paid')
        ->processed_at->not->toBeNull();
});

test('a duplicate stripe event id is rejected', function () {
    StripeWebhookEvent::factory()->create(['stripe_event_id' => 'evt_1']);

    StripeWebhookEvent::factory()->create(['stripe_event_id' => 'evt_1']);
})->throws(UniqueConstraintViolationException::class);

function subscribe(User $user, array $overrides = []): void
{
    $user->subscriptions()->create(array_merge([
        'type' => 'default',
        'stripe_id' => 'sub_'.fake()->unique()->numerify('######'),
        'stripe_status' => 'active',
        'stripe_price' => 'price_pro',
        'quantity' => 1,
    ], $overrides));
}

test('a user without a subscription is on the free plan', function () {
    expect(User::factory()->create()->currentPlan())->toBe(Plan::Free);
});

test('a user with an active subscription is on the pro plan', function () {
    $user = User::factory()->create();
    subscribe($user);

    expect($user->currentPlan())->toBe(Plan::Pro);
});

test('a user whose subscription has ended is on the free plan', function () {
    $user = User::factory()->create();
    subscribe($user, ['stripe_status' => 'canceled', 'ends_at' => now()->subDay()]);

    expect($user->currentPlan())->toBe(Plan::Free);
});

test('a cancelled subscription stays pro until its period ends', function () {
    $user = User::factory()->create();
    subscribe($user, ['ends_at' => now()->addWeek()]);

    expect($user->currentPlan())->toBe(Plan::Pro);
});

test('plan limits come from configuration', function () {
    expect(Plan::Free)->maxSpaces()->toBe(3)->maxTestimonialsPerSpace()->toBe(100)
        ->and(Plan::Pro)->maxSpaces()->toBe(25)->maxTestimonialsPerSpace()->toBe(1000);
});
