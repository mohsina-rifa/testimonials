<?php

use App\Actions\Analytics\ComputeOwnerAnalytics;
use App\Enums\EmbedLayout;
use App\Enums\Plan;
use App\Enums\SpaceField;
use App\Enums\Theme;
use App\Models\EmbedConfiguration;
use App\Models\Space;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('the seeder reproduces the sample data from the data model', function () {
    $this->seed(DatabaseSeeder::class);

    $alice = User::where('email', 'alice@acme.com')->firstOrFail();

    expect(Space::count())->toBe(4)
        ->and(Testimonial::count())->toBe(5)
        ->and(EmbedConfiguration::count())->toBe(4)
        ->and(app(ComputeOwnerAnalytics::class)->handle($alice))
        ->total_testimonials->toBe(3)
        ->unique_submitters->toBe(2);
});

test('enums expose the documented cases', function () {
    expect(array_column(Plan::cases(), 'value'))->toBe(['free', 'pro'])
        ->and(array_column(Theme::cases(), 'value'))->toBe(['light', 'dark', 'minimal'])
        ->and(array_column(EmbedLayout::cases(), 'value'))->toBe(['masonry', 'carousel'])
        ->and(array_column(SpaceField::cases(), 'value'))->toBe(['company', 'social_link', 'profile_photo']);
});
