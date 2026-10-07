<?php

use Inertia\Testing\AssertableInertia as Assert;

test('home page shows the marketing page with plan limits', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('welcome')
            ->where('plans.free.max_spaces', 3)
            ->where('plans.pro.max_testimonials_per_space', 1000));
});
