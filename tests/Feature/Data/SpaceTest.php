<?php

use App\Enums\SpaceField;
use App\Enums\Theme;
use App\Models\EmbedConfiguration;
use App\Models\Space;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Storage;

test('a user only sees their own spaces', function () {
    [$alice, $bob] = User::factory()->count(2)->create();
    Space::factory()->count(2)->for($alice)->create();
    Space::factory()->count(2)->for($bob)->create();

    expect($alice->spaces)->toHaveCount(2)
        ->and($alice->spaces->every(fn (Space $space) => $space->user->is($alice)))->toBeTrue();
});

test('a new space gets a ulid public id that never changes', function () {
    $space = Space::factory()->create();
    $publicId = $space->public_id;

    $space->update(['slug' => 'renamed-slug']);
    $space->public_id = 'tampered';
    $space->save();

    expect($publicId)->toHaveLength(26)
        ->and($space->fresh()->public_id)->toBe($publicId)
        ->and($space->fresh()->slug)->toBe('renamed-slug');
});

test('slugs must be unique but titles need not be', function () {
    Space::factory()->create(['title' => 'Same', 'slug' => 'one']);
    Space::factory()->create(['title' => 'Same', 'slug' => 'two']);

    expect(Space::where('title', 'Same')->count())->toBe(2);

    Space::factory()->create(['slug' => 'one']);
})->throws(UniqueConstraintViolationException::class);

test('theme defaults to light', function () {
    $created = User::factory()->create()->spaces()->create([
        'title' => 'T', 'subtitle' => 'S', 'ask' => 'A', 'slug' => 'defaults',
    ]);

    expect($created->fresh()->theme)->toBe(Theme::Light);
});

test('missing field configuration keys are disabled and optional', function () {
    $space = Space::factory()->create(['field_configuration' => [
        'company' => ['enabled' => true, 'required' => true],
    ]])->fresh();

    expect($space->isFieldEnabled(SpaceField::Company))->toBeTrue()
        ->and($space->isFieldRequired(SpaceField::Company))->toBeTrue()
        ->and($space->isFieldEnabled(SpaceField::SocialLink))->toBeFalse()
        ->and($space->isFieldRequired(SpaceField::SocialLink))->toBeFalse();
});

test('unknown field configuration keys are rejected', function () {
    Space::factory()->create(['field_configuration' => ['phone' => ['enabled' => true]]]);
})->throws(InvalidArgumentException::class);

test('deleting a space removes its testimonials, embed configuration and photos', function () {
    Storage::fake('public');
    Storage::disk('public')->put('photos/a.jpg', 'x');
    $space = Space::factory()->create();
    Testimonial::factory()->for($space)->create(['profile_photo_path' => 'photos/a.jpg']);

    $space->delete();

    expect(Testimonial::count())->toBe(0)
        ->and(EmbedConfiguration::count())->toBe(0)
        ->and(Storage::disk('public')->exists('photos/a.jpg'))->toBeFalse();
});

test('deleting a user removes their spaces and everything under them', function () {
    Storage::fake('public');
    Storage::disk('public')->put('photos/a.jpg', 'x');
    $user = User::factory()->create();
    $space = Space::factory()->for($user)->create();
    Testimonial::factory()->for($space)->create(['profile_photo_path' => 'photos/a.jpg']);

    $user->delete();

    expect(Space::count())->toBe(0)
        ->and(Testimonial::count())->toBe(0)
        ->and(EmbedConfiguration::count())->toBe(0)
        ->and(Storage::disk('public')->exists('photos/a.jpg'))->toBeFalse();
});
