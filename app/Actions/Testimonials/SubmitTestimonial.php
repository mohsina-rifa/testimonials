<?php

namespace App\Actions\Testimonials;

use App\Exceptions\PlanLimitReachedException;
use App\Models\Space;
use App\Models\Testimonial;
use Illuminate\Support\Facades\DB;

class SubmitTestimonial
{
    /**
     * Store a testimonial unless the space has reached its owner's plan limit.
     *
     * @param  array<string, mixed>  $attributes
     *
     * @throws PlanLimitReachedException
     */
    public function handle(Space $space, array $attributes): Testimonial
    {
        return DB::transaction(function () use ($space, $attributes): Testimonial {
            $lockedSpace = Space::query()->whereKey($space->getKey())->lockForUpdate()->firstOrFail();

            $limit = $lockedSpace->user->currentPlan()->maxTestimonialsPerSpace();

            if ($lockedSpace->testimonials()->count() >= $limit) {
                throw PlanLimitReachedException::forTestimonials();
            }

            return $lockedSpace->testimonials()->create($attributes);
        });
    }
}
