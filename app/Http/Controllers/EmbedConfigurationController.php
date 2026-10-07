<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmbedConfigurationRequest;
use App\Models\Space;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class EmbedConfigurationController extends Controller
{
    public function edit(Space $space): Response
    {
        Gate::authorize('manage', $space);

        $configuration = $space->embedConfiguration;

        return Inertia::render('spaces/embed', [
            'space' => ['id' => $space->id, 'title' => $space->title],
            'configuration' => [
                'layout' => $configuration->layout->value,
                'dark_mode' => $configuration->dark_mode,
                'animation_enabled' => $configuration->animation_enabled,
                'background_color' => $configuration->background_color,
                'show_rating' => $configuration->show_rating,
                'show_company' => $configuration->show_company,
                'show_profile_photo' => $configuration->show_profile_photo,
            ],
            'testimonials' => EmbedController::publicTestimonials($space),
            'embed_url' => route('embed.show', $space->public_id),
        ]);
    }

    public function update(UpdateEmbedConfigurationRequest $request, Space $space): RedirectResponse
    {
        Gate::authorize('manage', $space);

        $space->embedConfiguration->update($request->validated());

        return back()->with('success', 'Embed settings saved.');
    }
}
