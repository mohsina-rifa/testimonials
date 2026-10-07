<?php

namespace App\Actions\Billing;

use App\Models\Testimonial;
use App\Models\User;

class BuildPlanSummary
{
    /**
     * Summarize the owner's plan, limits and current usage for display.
     *
     * @return array{
     *     plan: string,
     *     max_spaces: int,
     *     max_testimonials_per_space: int,
     *     spaces_used: int,
     *     busiest_space_testimonials: int,
     *     over_limit: bool,
     *     billing_configured: bool,
     *     on_grace_period: bool,
     *     ends_at: string|null
     * }
     */
    public function handle(User $user): array
    {
        $plan = $user->currentPlan();
        $spacesUsed = $user->spaces()->count();

        $busiest = (int) Testimonial::query()
            ->whereIn('space_id', $user->spaces()->select('id'))
            ->selectRaw('count(*) as total')
            ->groupBy('space_id')
            ->orderByDesc('total')
            ->value('total');

        $subscription = $user->subscription();

        return [
            'plan' => $plan->value,
            'max_spaces' => $plan->maxSpaces(),
            'max_testimonials_per_space' => $plan->maxTestimonialsPerSpace(),
            'spaces_used' => $spacesUsed,
            'busiest_space_testimonials' => $busiest,
            'over_limit' => $spacesUsed > $plan->maxSpaces() || $busiest > $plan->maxTestimonialsPerSpace(),
            'billing_configured' => filled(config('cashier.secret')) && filled(config('cashier.pro_price')),
            'on_grace_period' => (bool) $subscription?->onGracePeriod(),
            'ends_at' => $subscription?->ends_at?->toDateString(),
        ];
    }
}
