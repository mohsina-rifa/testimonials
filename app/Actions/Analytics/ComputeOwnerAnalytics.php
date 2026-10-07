<?php

namespace App\Actions\Analytics;

use App\Models\Space;
use App\Models\Testimonial;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;

class ComputeOwnerAnalytics
{
    /**
     * Compute an owner's analytics live from stored testimonials.
     *
     * @param  int|null  $days  Number of days ending today, or null for all time.
     * @param  Space|null  $space  Restrict testimonial figures to one of the owner's spaces.
     * @return array{
     *     total_spaces: int,
     *     total_testimonials: int,
     *     unique_submitters: int,
     *     wall_of_love_count: int,
     *     collected_in_period: int,
     *     daily: array<string, int>
     * }
     */
    public function handle(User $owner, ?int $days = null, ?Space $space = null): array
    {
        $testimonials = fn (): Builder => Testimonial::query()
            ->whereIn('space_id', $owner->spaces()->select('id'))
            ->when($space, fn (Builder $query): Builder => $query->where('space_id', $space->id));

        $periodStart = $days === null ? null : Date::today(config('app.timezone'))->subDays($days - 1);

        $inPeriod = $testimonials();

        if ($periodStart !== null) {
            $inPeriod->where('created_at', '>=', $periodStart->utc());
        }

        $countsByDay = $inPeriod->pluck('created_at')
            ->countBy(fn (CarbonInterface $createdAt): string => $createdAt->copy()->timezone(config('app.timezone'))->toDateString());

        return [
            'total_spaces' => $owner->spaces()->count(),
            'total_testimonials' => $testimonials()->count(),
            'unique_submitters' => $testimonials()->distinct()->count('submitter_email'),
            'wall_of_love_count' => $testimonials()->where('is_wall_of_love', true)->count(),
            'collected_in_period' => $countsByDay->sum(),
            'daily' => $this->zeroFilledSeries($countsByDay->all(), $periodStart),
        ];
    }

    /**
     * @param  array<string, int>  $countsByDay
     * @return array<string, int>
     */
    private function zeroFilledSeries(array $countsByDay, ?CarbonInterface $start): array
    {
        if ($countsByDay === [] && $start === null) {
            return [];
        }

        $start ??= Date::parse(min(array_keys($countsByDay)), config('app.timezone'));
        $series = [];

        for ($day = $start; $day->lte(Date::today(config('app.timezone'))); $day = $day->addDay()) {
            $series[$day->toDateString()] = $countsByDay[$day->toDateString()] ?? 0;
        }

        return $series;
    }
}
