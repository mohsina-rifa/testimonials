<?php

namespace App\Http\Controllers;

use App\Actions\Billing\BuildPlanSummary;
use App\Actions\Spaces\CreateSpace;
use App\Exceptions\PlanLimitReachedException;
use App\Http\Requests\SpaceRequest;
use App\Models\Space;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SpaceController extends Controller
{
    public function index(Request $request, BuildPlanSummary $planSummary): Response
    {
        $spaces = $request->user()->spaces()->withCount('testimonials')->latest()->get()
            ->map(fn (Space $space): array => [
                'id' => $space->id,
                'title' => $space->title,
                'subtitle' => $space->subtitle,
                'slug' => $space->slug,
                'testimonials_count' => $space->testimonials_count,
                'collection_url' => route('collection.show', $space),
            ]);

        return Inertia::render('spaces/index', [
            'spaces' => $spaces,
            'plan' => $planSummary->handle($request->user()),
        ]);
    }

    public function create(Request $request, BuildPlanSummary $planSummary): Response
    {
        return Inertia::render('spaces/create', ['plan' => $planSummary->handle($request->user())]);
    }

    public function store(SpaceRequest $request, CreateSpace $createSpace): RedirectResponse
    {
        try {
            $space = $createSpace->handle($request->user(), $request->validated());
        } catch (PlanLimitReachedException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return to_route('spaces.edit', $space)->with('success', 'Space created.');
    }

    public function edit(Space $space): Response
    {
        Gate::authorize('manage', $space);

        return Inertia::render('spaces/edit', [
            'space' => [
                'id' => $space->id,
                'title' => $space->title,
                'subtitle' => $space->subtitle,
                'ask' => $space->ask,
                'slug' => $space->slug,
                'theme' => $space->theme->value,
                'rating_enabled' => $space->rating_enabled,
                'field_configuration' => $this->fieldConfiguration($space),
                'collection_url' => route('collection.show', $space),
            ],
        ]);
    }

    public function update(SpaceRequest $request, Space $space): RedirectResponse
    {
        Gate::authorize('manage', $space);

        $space->update($request->validated());

        return back()->with('success', 'Space updated.');
    }

    public function destroy(Space $space): RedirectResponse
    {
        Gate::authorize('manage', $space);

        $space->delete();

        return to_route('spaces.index')->with('success', 'Space deleted.');
    }

    /**
     * @return array<string, array{enabled: bool, required: bool}>
     */
    private function fieldConfiguration(Space $space): array
    {
        return collect(['company', 'social_link', 'profile_photo'])
            ->mapWithKeys(fn (string $field): array => [$field => [
                'enabled' => $space->field_configuration[$field]['enabled'] ?? false,
                'required' => $space->field_configuration[$field]['required'] ?? false,
            ]])
            ->all();
    }
}
