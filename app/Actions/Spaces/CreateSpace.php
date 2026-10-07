<?php

namespace App\Actions\Spaces;

use App\Exceptions\PlanLimitReachedException;
use App\Models\Space;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateSpace
{
    /**
     * Create a space for the user unless their plan limit is reached.
     *
     * @param  array<string, mixed>  $attributes
     *
     * @throws PlanLimitReachedException
     */
    public function handle(User $user, array $attributes): Space
    {
        return DB::transaction(function () use ($user, $attributes): Space {
            $lockedUser = User::query()->whereKey($user->getKey())->lockForUpdate()->firstOrFail();

            if ($lockedUser->spaces()->count() >= $lockedUser->currentPlan()->maxSpaces()) {
                throw PlanLimitReachedException::forSpaces();
            }

            return $lockedUser->spaces()->create($attributes);
        });
    }
}
