<?php

namespace App\Http\Controllers;

use App\Actions\Testimonials\SubmitTestimonial;
use App\Enums\SpaceField;
use App\Exceptions\PlanLimitReachedException;
use App\Http\Requests\SubmitTestimonialRequest;
use App\Models\Space;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CollectionController extends Controller
{
    public function show(Space $space): Response
    {
        $isFull = $space->testimonials()->count() >= $space->user->currentPlan()->maxTestimonialsPerSpace();

        return Inertia::render('collection/show', [
            'space' => [
                'title' => $space->title,
                'subtitle' => $space->subtitle,
                'ask' => $space->ask,
                'slug' => $space->slug,
                'theme' => $space->theme->value,
                'rating_enabled' => $space->rating_enabled,
                'fields' => collect(SpaceField::cases())->mapWithKeys(fn (SpaceField $field): array => [
                    $field->value => [
                        'enabled' => $space->isFieldEnabled($field),
                        'required' => $space->isFieldRequired($field),
                    ],
                ]),
            ],
            'unavailable' => $isFull,
            'submitted' => session('submitted', false),
        ]);
    }

    public function store(SubmitTestimonialRequest $request, Space $space, SubmitTestimonial $submitTestimonial): RedirectResponse
    {
        $attributes = [
            ...$request->safe()->except('profile_photo'),
            'consent_given' => true,
        ];

        if ($request->hasFile('profile_photo')) {
            $attributes['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        try {
            $submitTestimonial->handle($space, $attributes);
        } catch (PlanLimitReachedException) {
            return to_route('collection.show', $space);
        }

        return to_route('collection.show', $space)->with('submitted', true);
    }
}
