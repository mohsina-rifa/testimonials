<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Models\Testimonial;
use Inertia\Inertia;
use Inertia\Response;

class EmbedController extends Controller
{
    public function show(string $publicId): Response
    {
        $space = Space::query()->where('public_id', $publicId)->with('embedConfiguration')->firstOrFail();
        $configuration = $space->embedConfiguration;

        return Inertia::render('embed/show', [
            'title' => $space->title,
            'configuration' => [
                'layout' => $configuration->layout->value,
                'dark_mode' => $configuration->dark_mode,
                'animation_enabled' => $configuration->animation_enabled,
                'background_color' => $configuration->background_color,
                'show_rating' => $configuration->show_rating,
                'show_company' => $configuration->show_company,
                'show_profile_photo' => $configuration->show_profile_photo,
            ],
            'testimonials' => self::publicTestimonials($space),
        ]);
    }

    /**
     * Build the public-safe testimonial payload; submitter email is never included.
     *
     * @return list<array{id: int, name: string, company: string|null, text: string, rating: int|null, photo_url: string|null}>
     */
    public static function publicTestimonials(Space $space): array
    {
        return $space->testimonials()->publiclyVisible()->latest()->get()
            ->map(fn (Testimonial $testimonial): array => [
                'id' => $testimonial->id,
                'name' => $testimonial->submitter_name,
                'company' => $testimonial->company_name,
                'text' => $testimonial->testimonial_text,
                'rating' => $testimonial->rating,
                'photo_url' => $testimonial->profile_photo_url,
            ])
            ->all();
    }
}
