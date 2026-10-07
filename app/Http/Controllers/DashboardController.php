<?php

namespace App\Http\Controllers;

use App\Actions\Analytics\ComputeOwnerAnalytics;
use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request, Space $space, ComputeOwnerAnalytics $analytics): Response
    {
        Gate::authorize('manage', $space);

        $period = in_array($request->query('period'), ['7', '30', '90'], true) ? $request->query('period') : 'all';
        $result = $analytics->handle($request->user(), $period === 'all' ? null : (int) $period, $space);

        return Inertia::render('dashboard', [
            'space' => ['id' => $space->id, 'title' => $space->title, 'collection_url' => route('collection.show', $space)],
            'period' => $period,
            'analytics' => [
                ...$result,
                'daily' => collect($result['daily'])
                    ->map(fn (int $count, string $date): array => ['date' => $date, 'count' => $count])
                    ->values(),
            ],
        ]);
    }
}
