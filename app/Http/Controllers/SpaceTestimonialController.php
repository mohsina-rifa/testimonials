<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTestimonialRequest;
use App\Models\Space;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SpaceTestimonialController extends Controller
{
    public function index(Request $request, Space $space): Response
    {
        Gate::authorize('manage', $space);

        $filter = in_array($request->query('filter'), ['favorites', 'wall', 'hidden'], true) ? $request->query('filter') : 'all';

        $testimonials = $space->testimonials()
            ->when($filter === 'favorites', fn ($query) => $query->where('is_favorite', true))
            ->when($filter === 'wall', fn ($query) => $query->where('is_wall_of_love', true))
            ->when($filter === 'hidden', fn ($query) => $query->where('is_hidden', true))
            ->latest()
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Testimonial $testimonial): array => [
                'id' => $testimonial->id,
                'submitter_name' => $testimonial->submitter_name,
                'submitter_email' => $testimonial->submitter_email,
                'company_name' => $testimonial->company_name,
                'social_link' => $testimonial->social_link,
                'profile_photo_url' => $testimonial->profile_photo_url,
                'testimonial_text' => $testimonial->testimonial_text,
                'rating' => $testimonial->rating,
                'consent_given' => $testimonial->consent_given,
                'is_favorite' => $testimonial->is_favorite,
                'is_wall_of_love' => $testimonial->is_wall_of_love,
                'is_hidden' => $testimonial->is_hidden,
                'created_at' => $testimonial->created_at?->toDateString(),
            ]);

        return Inertia::render('spaces/testimonials', [
            'space' => ['id' => $space->id, 'title' => $space->title],
            'testimonials' => $testimonials,
            'filter' => $filter,
        ]);
    }

    public function update(UpdateTestimonialRequest $request, Space $space, Testimonial $testimonial): RedirectResponse
    {
        Gate::authorize('manage', $space);

        $testimonial->update($request->validated());

        return back();
    }

    public function destroy(Space $space, Testimonial $testimonial): RedirectResponse
    {
        Gate::authorize('manage', $space);

        $testimonial->delete();

        return back()->with('success', 'Testimonial deleted.');
    }
}
