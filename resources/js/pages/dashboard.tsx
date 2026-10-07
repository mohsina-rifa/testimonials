import { Head, router } from '@inertiajs/react';
import { BarChart3, MessageSquareHeart } from 'lucide-react';
import DailyChart from '@/components/daily-chart';
import type { DailyPoint } from '@/components/daily-chart';
import EmptyState from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';
import { dashboard } from '@/routes/spaces';

type Analytics = {
    total_testimonials: number;
    unique_submitters: number;
    wall_of_love_count: number;
    collected_in_period: number;
    daily: DailyPoint[];
};

const periods = [
    { value: '7', label: '7 days' },
    { value: '30', label: '30 days' },
    { value: '90', label: '90 days' },
    { value: 'all', label: 'All time' },
];

export default function Dashboard({
    space,
    period,
    analytics,
}: {
    space: { id: number; title: string; collection_url: string };
    period: string;
    analytics: Analytics;
}) {
    const stats = [
        { label: 'Testimonials', value: analytics.total_testimonials },
        { label: 'On Wall of Love', value: analytics.wall_of_love_count },
        { label: 'Unique submitters', value: analytics.unique_submitters },
    ];

    return (
        <>
            <Head title="Dashboard" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div>
                    <h1 className="text-2xl font-semibold">{space.title}</h1>
                    <p className="text-sm text-muted-foreground">Dashboard</p>
                </div>

                {analytics.total_testimonials === 0 ? (
                    <EmptyState
                        icon={MessageSquareHeart}
                        title="No testimonials yet"
                        description="Share your collection page to start receiving testimonials."
                    >
                        <Button asChild>
                            <a
                                href={space.collection_url}
                                target="_blank"
                                rel="noreferrer"
                            >
                                Open collection page
                            </a>
                        </Button>
                    </EmptyState>
                ) : (
                    <>
                        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            {stats.map((stat) => (
                                <Card key={stat.label}>
                                    <CardHeader className="pb-2">
                                        <CardTitle className="text-sm font-medium text-muted-foreground">
                                            {stat.label}
                                        </CardTitle>
                                    </CardHeader>
                                    <CardContent className="text-3xl font-bold">
                                        {stat.value}
                                    </CardContent>
                                </Card>
                            ))}
                        </div>

                        <Card>
                            <CardHeader className="flex flex-row flex-wrap items-center justify-between gap-2">
                                <CardTitle className="flex items-center gap-2">
                                    <BarChart3 className="size-5 text-primary" />
                                    {analytics.collected_in_period} collected
                                </CardTitle>
                                <ToggleGroup
                                    type="single"
                                    variant="outline"
                                    size="sm"
                                    value={period}
                                    onValueChange={(value) =>
                                        value &&
                                        router.get(
                                            dashboard(space.id),
                                            { period: value },
                                            {
                                                preserveScroll: true,
                                                preserveState: true,
                                            },
                                        )
                                    }
                                >
                                    {periods.map((item) => (
                                        <ToggleGroupItem
                                            key={item.value}
                                            value={item.value}
                                        >
                                            {item.label}
                                        </ToggleGroupItem>
                                    ))}
                                </ToggleGroup>
                            </CardHeader>
                            <CardContent>
                                {analytics.daily.length > 0 ? (
                                    <DailyChart data={analytics.daily} />
                                ) : (
                                    <p className="py-8 text-center text-sm text-muted-foreground">
                                        No testimonials collected yet.
                                    </p>
                                )}
                            </CardContent>
                        </Card>
                    </>
                )}
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [{ title: 'Spaces', href: '/spaces' }],
};
