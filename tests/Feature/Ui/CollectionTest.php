<?php

use App\Models\Space;
use App\Models\Testimonial;
use Inertia\Testing\AssertableInertia as Assert;

function submission(array $overrides = []): array
{
    return [
        'submitter_name' => 'Dana',
        'submitter_email' => 'Dana@Example.com',
        'testimonial_text' => 'Great product',
        'rating' => 5,
        'consent_given' => true,
        ...$overrides,
    ];
}

test('visitors see the collection page and unknown slugs 404', function () {
    $space = Space::factory()->create(['slug' => 'acme']);

    $this->get(route('collection.show', $space))
        ->assertInertia(fn (Assert $page) => $page
            ->component('collection/show')
            ->where('unavailable', false)
            ->where('space.fields.company.enabled', false));

    $this->get('/s/missing')->assertNotFound();
});

test('visitor submits a testimonial', function () {
    $space = Space::factory()->create();

    $this->post(route('collection.store', $space), submission())
        ->assertRedirect(route('collection.show', $space))
        ->assertSessionHas('submitted', true);

    $testimonial = $space->testimonials()->first();
    expect($testimonial->submitter_email)->toBe('dana@example.com')
        ->and($testimonial->consent_given)->toBeTrue();
});

test('required optional fields are enforced and disabled ones rejected', function () {
    $space = Space::factory()->create(['field_configuration' => [
        'company' => ['enabled' => true, 'required' => true],
    ]]);

    $this->post(route('collection.store', $space), submission())->assertSessionHasErrors('company_name');
    $this->post(route('collection.store', $space), submission(['company_name' => 'Acme', 'social_link' => 'https://x.com/a']))
        ->assertSessionHasErrors('social_link');

    expect($space->testimonials()->count())->toBe(0);
});

test('rating is required only when enabled and consent is mandatory', function () {
    $space = Space::factory()->create(['rating_enabled' => true]);
    $this->post(route('collection.store', $space), submission(['rating' => null]))->assertSessionHasErrors('rating');
    $this->post(route('collection.store', $space), submission(['consent_given' => false]))->assertSessionHasErrors('consent_given');

    $noRating = Space::factory()->create(['rating_enabled' => false]);
    $this->post(route('collection.store', $noRating), submission(['rating' => null]))->assertSessionHasNoErrors();
});

test('full space shows the unavailable state and refuses submissions', function () {
    $space = Space::factory()->create();
    Testimonial::factory()->count(100)->for($space)->create();

    $this->get(route('collection.show', $space))
        ->assertInertia(fn (Assert $page) => $page->where('unavailable', true));

    $this->post(route('collection.store', $space), submission())->assertRedirect();

    expect($space->testimonials()->count())->toBe(100);
});
