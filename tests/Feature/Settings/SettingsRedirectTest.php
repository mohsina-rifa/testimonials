<?php

use App\Models\User;

test('the settings index redirects to the profile page', function () {
    $this->actingAs(User::factory()->create())
        ->get('/settings')
        ->assertRedirect('/settings/profile');
});

test('guests are redirected to login from the settings index', function () {
    $this->get('/settings')->assertRedirect(route('login'));
});
