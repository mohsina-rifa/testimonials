<?php

namespace App\Http\Controllers;

use App\Actions\Analytics\ComputeOwnerAnalytics;
use App\Actions\Billing\BuildPlanSummary;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request, ComputeOwnerAnalytics $analytics, BuildPlanSummary $planSummary): Response
    {
        $period = in_array($request->query('period'), ['7', '30', '90'], true) ? $request->query('period') : 'all';
        $result = $analytics->handle($request->user(), $period === 'all' ? null : (int) $period);

        return Inertia::render('dashboard', [
            'period' => $period,
            'analytics' => [
                ...$result,
                'daily' => collect($result['daily'])
                    ->map(fn (int $count, string $date): array => ['date' => $date, 'count' => $count])
                    ->values(),
            ],
            'plan' => $planSummary->handle($request->user()),
        ]);
    }
}
