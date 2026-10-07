<?php

use App\Models\Space;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function subscribeToPro(User $user, array $attributes = []): void
{
    $user->update(['stripe_id' => 'cus_test']);
    $user->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_'.fake()->unique()->numerify('####'),
        'stripe_status' => 'active',
        'stripe_price' => 'price_pro',
        'quantity' => 1,
        ...$attributes,
    ]);
}

test('guests are redirected from billing', function () {
    $this->get(route('billing.index'))->assertRedirect(route('login'));
});

test('free owner sees plan limits and billing availability', function () {
    config(['cashier.secret' => null, 'cashier.pro_price' => null]);

    $this->actingAs(User::factory()->create())->get(route('billing.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('billing')
            ->where('plan.plan', 'free')
            ->where('plan.max_spaces', 3)
            ->where('plan.billing_configured', false)
            ->where('plan.over_limit', false));
});

test('pro owner sees pro limits', function () {
    $user = User::factory()->create();
    subscribeToPro($user);

    $this->actingAs($user)->get(route('billing.index'))
        ->assertInertia(fn (Assert $page) => $page->where('plan.plan', 'pro')->where('plan.max_spaces', 25));
});

test('cancelled owner in the paid period is still pro with an end date', function () {
    $user = User::factory()->create();
    subscribeToPro($user, ['ends_at' => now()->addDays(5)]);

    $this->actingAs($user)->get(route('billing.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('plan.plan', 'pro')
            ->where('plan.on_grace_period', true)
            ->where('plan.ends_at', now()->addDays(5)->toDateString()));
});

test('lapsed owner above free limits sees the over limit state', function () {
    $user = User::factory()->create();
    Space::factory()->for($user)->count(4)->create();

    $this->actingAs($user)->get(route('billing.index'))
        ->assertInertia(fn (Assert $page) => $page->where('plan.over_limit', true)->where('plan.spaces_used', 4));
});

test('checkout is refused when billing is not configured', function () {
    config(['cashier.secret' => null, 'cashier.pro_price' => null]);

    $this->actingAs(User::factory()->create())->post(route('billing.checkout'))->assertSessionHas('error');
});

test('portal is refused for owners without a billing account', function () {
    config(['cashier.secret' => 'sk_test', 'cashier.pro_price' => 'price_pro']);

    $this->actingAs(User::factory()->create())->get(route('billing.portal'))->assertSessionHas('error');
});
