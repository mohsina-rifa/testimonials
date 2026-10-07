import { Head, Link, usePage } from '@inertiajs/react';
import {
    BarChart3,
    Check,
    Code2,
    FolderKanban,
    MessageSquareHeart,
    ShieldCheck,
} from 'lucide-react';
import { ThemeToggle } from '@/components/theme-toggle';
import AppLogoIcon from '@/components/app-logo-icon';
import { Button } from '@/components/ui/button';
import { login, register } from '@/routes';
import { index as spaces } from '@/routes/spaces';

type PlanLimits = { max_spaces: number; max_testimonials_per_space: number };

const features = [
    {
        icon: FolderKanban,
        title: 'Collection spaces',
        text: 'Create a branded page per product or campaign and share one link to collect testimonials.',
    },
    {
        icon: ShieldCheck,
        title: 'Consent first',
        text: 'Only testimonials with consent can reach your Wall of Love, and you can hide any of them at any time.',
    },
    {
        icon: Code2,
        title: 'Embeddable wall',
        text: 'Show your best testimonials anywhere with a masonry or carousel embed that matches your site.',
    },
    {
        icon: BarChart3,
        title: 'Simple analytics',
        text: 'Track submissions over 7, 30 or 90 days and see how many unique people have shared feedback.',
    },
];

export default function Welcome({
    plans,
}: {
    plans: { free: PlanLimits; pro: PlanLimits };
}) {
    const { auth, name } = usePage().props;

    const tiers = [
        {
            name: 'Free',
            price: '$0',
            limits: plans.free,
            highlighted: false,
        },
        { name: 'Pro', price: 'Paid', limits: plans.pro, highlighted: true },
    ];

    return (
        <>
            <Head title="Collect and showcase testimonials" />
            <div className="min-h-screen bg-background text-foreground">
                <header className="border-b">
                    <div className="mx-auto flex max-w-6xl items-center justify-between p-4">
                        <Link
                            href="/"
                            className="flex items-center gap-2 font-semibold"
                        >
                            <AppLogoIcon className="size-7" />
                            {name}
                        </Link>
                        <nav className="flex items-center gap-2">
                            {auth.user ? (
                                <Button asChild>
                                    <Link href={spaces()}>My spaces</Link>
                                </Button>
                            ) : (
                                <>
                                    <Button asChild variant="ghost">
                                        <Link href={login()}>Log in</Link>
                                    </Button>
                                    <Button asChild>
                                        <Link href={register()}>
                                            Get started
                                        </Link>
                                    </Button>
                                </>
                            )}
                            <ThemeToggle />
                        </nav>
                    </div>
                </header>

                <section className="mx-auto max-w-4xl px-4 py-20 text-center">
                    <MessageSquareHeart className="mx-auto mb-4 size-10 text-primary" />
                    <h1 className="text-4xl font-bold tracking-tight sm:text-5xl">
                        Turn happy customers into your best marketing
                    </h1>
                    <p className="mx-auto mt-4 max-w-2xl text-lg text-muted-foreground">
                        Collect testimonials with a shareable page, choose what
                        goes public, and embed a Wall of Love on your website.
                    </p>
                    <div className="mt-8 flex justify-center gap-3">
                        <Button asChild size="lg">
                            <Link href={auth.user ? spaces() : register()}>
                                {auth.user
                                    ? 'Go to your spaces'
                                    : 'Start for free'}
                            </Link>
                        </Button>
                    </div>
                </section>

                <section className="border-t bg-muted/40 py-16">
                    <div className="mx-auto grid max-w-6xl gap-6 px-4 sm:grid-cols-2 lg:grid-cols-4">
                        {features.map((feature) => (
                            <div
                                key={feature.title}
                                className="rounded-xl border bg-card p-5"
                            >
                                <feature.icon className="mb-3 size-6 text-primary" />
                                <h3 className="font-semibold">
                                    {feature.title}
                                </h3>
                                <p className="mt-1 text-sm text-muted-foreground">
                                    {feature.text}
                                </p>
                            </div>
                        ))}
                    </div>
                </section>

                <section className="mx-auto max-w-4xl px-4 py-16" id="pricing">
                    <h2 className="text-center text-3xl font-bold">
                        Simple pricing
                    </h2>
                    <div className="mt-8 grid gap-6 sm:grid-cols-2">
                        {tiers.map((tier) => (
                            <div
                                key={tier.name}
                                className={
                                    tier.highlighted
                                        ? 'rounded-xl border-2 border-primary p-6'
                                        : 'rounded-xl border p-6'
                                }
                            >
                                <h3 className="text-xl font-semibold">
                                    {tier.name}
                                </h3>
                                <ul className="mt-4 space-y-2 text-sm">
                                    <li className="flex items-center gap-2">
                                        <Check className="size-4 text-green-600" />
                                        {tier.limits.max_spaces} spaces
                                    </li>
                                    <li className="flex items-center gap-2">
                                        <Check className="size-4 text-green-600" />
                                        {tier.limits.max_testimonials_per_space.toLocaleString()}{' '}
                                        testimonials per space
                                    </li>
                                    <li className="flex items-center gap-2">
                                        <Check className="size-4 text-green-600" />
                                        Wall of Love embed and analytics
                                    </li>
                                </ul>
                                <Button
                                    asChild
                                    className="mt-6 w-full"
                                    variant={
                                        tier.highlighted ? 'default' : 'outline'
                                    }
                                >
                                    <Link
                                        href={auth.user ? spaces() : register()}
                                    >
                                        {tier.highlighted
                                            ? 'Choose Pro'
                                            : 'Start free'}
                                    </Link>
                                </Button>
                            </div>
                        ))}
                    </div>
                </section>

                <footer className="border-t py-6 text-center text-sm text-muted-foreground">
                    © {new Date().getFullYear()} {name}
                </footer>
            </div>
        </>
    );
}
