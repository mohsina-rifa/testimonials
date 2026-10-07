import { Head, Link } from '@inertiajs/react';
import { ExternalLink, FolderKanban } from 'lucide-react';
import EmptyState from '@/components/empty-state';
import UpgradePrompt from '@/components/upgrade-prompt';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { create, index } from '@/routes/spaces';
import { dashboard, edit } from '@/routes/spaces';
import { index as testimonials } from '@/routes/spaces/testimonials';

type SpaceSummary = {
    id: number;
    title: string;
    subtitle: string;
    slug: string;
    testimonials_count: number;
    collection_url: string;
};

export default function SpacesIndex({
    spaces,
    plan,
}: {
    spaces: SpaceSummary[];
    plan: { max_spaces: number; spaces_used: number };
}) {
    const atLimit = plan.spaces_used >= plan.max_spaces;

    return (
        <>
            <Head title="Spaces" />
            <div className="flex flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold">Spaces</h1>
                        <p className="text-sm text-muted-foreground">
                            {plan.spaces_used} of {plan.max_spaces} spaces used
                        </p>
                    </div>
                    {atLimit ? (
                        <Button disabled>New space</Button>
                    ) : (
                        <Button asChild>
                            <Link href={create()}>New space</Link>
                        </Button>
                    )}
                </div>

                {atLimit && (
                    <UpgradePrompt message="You have reached the space limit for your plan." />
                )}

                {spaces.length === 0 ? (
                    <EmptyState
                        icon={FolderKanban}
                        title="No spaces yet"
                        description="Create a space to start collecting testimonials."
                    />
                ) : (
                    <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        {spaces.map((space) => (
                            <Card key={space.id}>
                                <CardHeader>
                                    <CardTitle>
                                        <Link href={dashboard(space.id)} className="hover:underline">
                                            {space.title}
                                        </Link>
                                    </CardTitle>
                                    <CardDescription>{space.subtitle}</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-3">
                                    <p className="text-sm text-muted-foreground">
                                        {space.testimonials_count} testimonials
                                    </p>
                                    <div className="flex flex-wrap gap-2">
                                        <Button asChild size="sm">
                                            <Link href={dashboard(space.id)}>Dashboard</Link>
                                        </Button>
                                        <Button asChild size="sm" variant="outline">
                                            <Link href={testimonials(space.id)}>Inbox</Link>
                                        </Button>
                                        <Button asChild size="sm" variant="outline">
                                            <Link href={edit(space.id)}>Space</Link>
                                        </Button>
                                        <Button asChild size="sm" variant="ghost">
                                            <a href={space.collection_url} target="_blank" rel="noreferrer">
                                                <ExternalLink /> Public page
                                            </a>
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}
            </div>
        </>
    );
}

SpacesIndex.layout = {
    breadcrumbs: [{ title: 'Spaces', href: index() }],
};
