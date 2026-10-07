import { Head, router } from '@inertiajs/react';
import { Check, CircleAlert } from 'lucide-react';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { checkout, index, portal } from '@/routes/billing';

type Plan = {
    plan: 'free' | 'pro';
    max_spaces: number;
    max_testimonials_per_space: number;
    spaces_used: number;
    busiest_space_testimonials: number;
    over_limit: boolean;
    billing_configured: boolean;
    on_grace_period: boolean;
    ends_at: string | null;
};

function Usage({ label, used, max }: { label: string; used: number; max: number }) {
    const percent = Math.min(100, (used / max) * 100);

    return (
        <div>
            <div className="mb-1 flex justify-between text-sm">
                <span>{label}</span>
                <span className="text-muted-foreground">
                    {used} of {max.toLocaleString()}
                </span>
            </div>
            <div className="h-2 rounded-full bg-muted">
                <div
                    className={percent >= 100 ? 'h-2 rounded-full bg-amber-500' : 'h-2 rounded-full bg-primary'}
                    style={{ width: `${percent}%` }}
                />
            </div>
        </div>
    );
}

export default function Billing({ plan }: { plan: Plan }) {
    const isPro = plan.plan === 'pro';

    return (
        <>
            <Head title="Billing" />
            <div className="mx-auto flex w-full max-w-3xl flex-col gap-6 p-4">
                <h1 className="text-2xl font-semibold">Billing</h1>

                {!plan.billing_configured && (
                    <Alert>
                        <CircleAlert />
                        <AlertTitle>Billing unavailable</AlertTitle>
                        <AlertDescription>
                            Payments are not configured for this environment, so plan changes are disabled.
                        </AlertDescription>
                    </Alert>
                )}

                {plan.over_limit && (
                    <Alert className="border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-100">
                        <CircleAlert className="text-amber-600" />
                        <AlertTitle>You are over your plan limits</AlertTitle>
                        <AlertDescription className="text-inherit">
                            Your existing data is kept, but new spaces or testimonials are blocked until you are under the limit or upgrade.
                        </AlertDescription>
                    </Alert>
                )}

                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-2">
                            <CardTitle>Current plan</CardTitle>
                            <Badge variant={isPro ? 'default' : 'secondary'}>
                                {isPro ? 'Pro' : 'Free'}
                            </Badge>
                        </div>
                        <CardDescription>
                            {plan.on_grace_period && plan.ends_at
                                ? `Cancelled — Pro access ends on ${plan.ends_at}.`
                                : isPro
                                  ? 'Your Pro subscription is active.'
                                  : 'Upgrade to Pro for more spaces and testimonials.'}
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <Usage label="Spaces" used={plan.spaces_used} max={plan.max_spaces} />
                        <Usage
                            label="Testimonials in your busiest space"
                            used={plan.busiest_space_testimonials}
                            max={plan.max_testimonials_per_space}
                        />
                        <ul className="space-y-1 text-sm">
                            <li className="flex items-center gap-2">
                                <Check className="size-4 text-green-600" />
                                Up to {plan.max_spaces} spaces
                            </li>
                            <li className="flex items-center gap-2">
                                <Check className="size-4 text-green-600" />
                                Up to {plan.max_testimonials_per_space.toLocaleString()} testimonials per space
                            </li>
                        </ul>
                        <div className="flex gap-2">
                            {!isPro && (
                                <Button
                                    disabled={!plan.billing_configured}
                                    onClick={() => router.post(checkout.url())}
                                >
                                    Upgrade to Pro
                                </Button>
                            )}
                            {isPro && (
                                <Button asChild variant="outline" disabled={!plan.billing_configured}>
                                    <a href={portal.url()}>Manage subscription</a>
                                </Button>
                            )}
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

Billing.layout = {
    breadcrumbs: [{ title: 'Billing', href: index() }],
};
