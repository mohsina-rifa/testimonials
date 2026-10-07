<?php

use App\Models\Space;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

test('submitter email is stored trimmed and lowercased', function () {
    $testimonial = Testimonial::factory()->create(['submitter_email' => '  Dana@Gmail.COM ']);

    expect($testimonial->fresh()->submitter_email)->toBe('dana@gmail.com');
});

test('the same email in two spaces creates independent testimonials', function () {
    $first = Testimonial::factory()->create(['submitter_email' => 'dana@gmail.com', 'submitter_name' => 'Dana']);
    $second = Testimonial::factory()->create(['submitter_email' => 'dana@gmail.com', 'submitter_name' => 'Dana']);

    $first->update(['submitter_name' => 'Dana R.']);

    expect($second->fresh()->submitter_name)->toBe('Dana');
});

test('flags default to false', function () {
    $testimonial = Space::factory()->create()->testimonials()->create([
        'submitter_name' => 'A', 'submitter_email' => 'a@b.co', 'testimonial_text' => 'Hi',
    ])->fresh();

    expect($testimonial)
        ->consent_given->toBeFalse()
        ->is_favorite->toBeFalse()
        ->is_wall_of_love->toBeFalse()
        ->is_hidden->toBeFalse();
});

test('ratings must be between 1 and 5', function (int $rating) {
    Testimonial::factory()->create(['rating' => $rating]);
})->with([0, 6])->throws(ValidationException::class);

test('a testimonial may have no rating', function () {
    expect(Testimonial::factory()->create(['rating' => null])->rating)->toBeNull();
});

test('turning ratings off keeps stored ratings', function () {
    $space = Space::factory()->create();
    Testimonial::factory()->for($space)->create(['rating' => 4]);

    $space->update(['rating_enabled' => false]);

    expect($space->testimonials()->first()->rating)->toBe(4);
});

test('disabling an optional field keeps stored values', function () {
    $space = Space::factory()->create(['field_configuration' => ['company' => ['enabled' => true]]]);
    Testimonial::factory()->for($space)->create(['company_name' => 'Globex']);

    $space->update(['field_configuration' => ['company' => ['enabled' => false]]]);

    expect($space->testimonials()->first()->company_name)->toBe('Globex');
});

test('the profile photo url is derived from the stored path', function () {
    Storage::fake('public');

    $with = Testimonial::factory()->create(['profile_photo_path' => 'photos/a.jpg']);
    $without = Testimonial::factory()->create();

    expect($with->profile_photo_url)->toBe(Storage::disk('public')->url('photos/a.jpg'))
        ->and($without->profile_photo_url)->toBeNull();
});

test('deleting a testimonial removes its photo file', function () {
    Storage::fake('public');
    Storage::disk('public')->put('photos/a.jpg', 'x');
    $testimonial = Testimonial::factory()->create(['profile_photo_path' => 'photos/a.jpg']);

    $testimonial->delete();

    expect(Storage::disk('public')->exists('photos/a.jpg'))->toBeFalse();
});

test('only consented, unhidden wall of love testimonials are publicly visible', function () {
    $space = Space::factory()->create();
    $visible = Testimonial::factory()->for($space)->onWallOfLove()->favorite()->create();
    Testimonial::factory()->for($space)->onWallOfLove()->hidden()->create();
    Testimonial::factory()->for($space)->consented()->create();
    Testimonial::factory()->for($space)->create();

    expect(Testimonial::publiclyVisible()->pluck('id')->all())->toBe([$visible->id]);
});

test('wall of love cannot be enabled without consent', function () {
    $testimonial = Testimonial::factory()->create();

    expect(fn () => $testimonial->update(['is_wall_of_love' => true]))->toThrow(ValidationException::class)
        ->and($testimonial->fresh()->is_wall_of_love)->toBeFalse();
});

test('wall of love can be enabled with consent', function () {
    $testimonial = Testimonial::factory()->consented()->create();

    $testimonial->update(['is_wall_of_love' => true]);

    expect($testimonial->fresh()->is_wall_of_love)->toBeTrue();
});
