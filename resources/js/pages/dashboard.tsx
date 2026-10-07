import { Head, Link, router } from '@inertiajs/react';
import { BarChart3, FolderKanban } from 'lucide-react';
import DailyChart from '@/components/daily-chart';
import type { DailyPoint } from '@/components/daily-chart';
import EmptyState from '@/components/empty-state';
import UpgradePrompt from '@/components/upgrade-prompt';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';
import { dashboard } from '@/routes';
import { create } from '@/routes/spaces';

type Analytics = {
    total_spaces: number;
    total_testimonials: number;
    unique_submitters: number;
    wall_of_love_count: number;
    collected_in_period: number;
    daily: DailyPoint[];
};

type Plan = {
    plan: string;
    max_spaces: number;
    spaces_used: number;
};

const periods = [
    { value: '7', label: '7 days' },
    { value: '30', label: '30 days' },
    { value: '90', label: '90 days' },
    { value: 'all', label: 'All time' },
];

export default function Dashboard({
    period,
    analytics,
    plan,
}: {
    period: string;
    analytics: Analytics;
    plan: Plan;
}) {
    const stats = [
        { label: 'Spaces', value: analytics.total_spaces },
        { label: 'Testimonials', value: analytics.total_testimonials },
        { label: 'On Wall of Love', value: analytics.wall_of_love_count },
        { label: 'Unique submitters', value: analytics.unique_submitters },
    ];

    return (
        <>
            <Head title="Dashboard" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div className="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h1 className="text-2xl font-semibold">Dashboard</h1>
                        <p className="text-sm text-muted-foreground">
                            <span className="capitalize">{plan.plan}</span> plan · {plan.spaces_used} of {plan.max_spaces} spaces used
                        </p>
                    </div>
                    <Button asChild variant="outline" size="sm">
                        <Link href={create()}>New space</Link>
                    </Button>
                </div>

                {plan.spaces_used >= plan.max_spaces && plan.plan === 'free' && (
                    <UpgradePrompt message="You are using all of your Free spaces. Upgrade to create more." />
                )}

                {analytics.total_spaces === 0 ? (
                    <EmptyState
                        icon={FolderKanban}
                        title="Create your first space"
                        description="A space is a shareable page where people submit testimonials."
                    >
                        <Button asChild>
                            <Link href={create()}>Create a space</Link>
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
                                            dashboard(),
                                            { period: value },
                                            { preserveScroll: true, preserveState: true },
                                        )
                                    }
                                >
                                    {periods.map((item) => (
                                        <ToggleGroupItem key={item.value} value={item.value}>
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
    breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
};
