import { Head, Link, router } from '@inertiajs/react';
import { Heart, MessageSquare, Trash2 } from 'lucide-react';
import { useState } from 'react';
import EmptyState from '@/components/empty-state';
import StarRating from '@/components/star-rating';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { index as spacesIndex } from '@/routes/spaces';
import { destroy, index, update } from '@/routes/spaces/testimonials';

type Testimonial = {
    id: number;
    submitter_name: string;
    submitter_email: string;
    company_name: string | null;
    social_link: string | null;
    profile_photo_url: string | null;
    testimonial_text: string;
    rating: number | null;
    consent_given: boolean;
    is_favorite: boolean;
    is_wall_of_love: boolean;
    is_hidden: boolean;
    created_at: string;
};

type Paginated = {
    data: Testimonial[];
    links: { url: string | null; label: string; active: boolean }[];
};

const filters = [
    { value: 'all', label: 'All' },
    { value: 'favorites', label: 'Favorites' },
    { value: 'wall', label: 'Wall of Love' },
    { value: 'hidden', label: 'Hidden' },
];

export default function SpaceTestimonials({
    space,
    testimonials,
    filter,
}: {
    space: { id: number; title: string };
    testimonials: Paginated;
    filter: string;
}) {
    const [deleting, setDeleting] = useState<Testimonial | null>(null);

    const toggle = (testimonial: Testimonial, field: 'is_favorite' | 'is_hidden' | 'is_wall_of_love') =>
        router.patch(
            update.url({ space: space.id, testimonial: testimonial.id }),
            { [field]: !testimonial[field] },
            { preserveScroll: true },
        );

    return (
        <>
            <Head title={`${space.title} testimonials`} />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <h1 className="text-2xl font-semibold">{space.title}</h1>

                <div className="flex flex-wrap gap-2">
                    {filters.map((item) => (
                        <Button
                            key={item.value}
                            asChild
                            size="sm"
                            variant={filter === item.value ? 'default' : 'outline'}
                        >
                            <Link
                                href={index.url(space.id, {
                                    query: item.value === 'all' ? {} : { filter: item.value },
                                })}
                            >
                                {item.label}
                            </Link>
                        </Button>
                    ))}
                </div>

                {testimonials.data.length === 0 ? (
                    <EmptyState
                        icon={MessageSquare}
                        title="No testimonials here"
                        description="Share your collection page to start receiving testimonials."
                    />
                ) : (
                    <div className="grid gap-4">
                        {testimonials.data.map((testimonial) => (
                            <article key={testimonial.id} className="rounded-xl border p-4">
                                <div className="flex flex-wrap items-start justify-between gap-2">
                                    <div className="flex items-center gap-3">
                                        {testimonial.profile_photo_url && (
                                            <img src={testimonial.profile_photo_url} alt="" className="size-10 rounded-full object-cover" />
                                        )}
                                        <div>
                                            <div className="font-medium">{testimonial.submitter_name}</div>
                                            <div className="text-xs text-muted-foreground">
                                                {testimonial.submitter_email}
                                                {testimonial.company_name && ` · ${testimonial.company_name}`}
                                                {` · ${testimonial.created_at}`}
                                            </div>
                                        </div>
                                    </div>
                                    {testimonial.rating !== null && <StarRating value={testimonial.rating} />}
                                </div>
                                <p className="mt-3 text-sm">{testimonial.testimonial_text}</p>
                                <div className="mt-3 flex flex-wrap items-center gap-2">
                                    {testimonial.consent_given ? (
                                        <Badge className="bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-200">Consent given</Badge>
                                    ) : (
                                        <Badge className="bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200">No consent</Badge>
                                    )}
                                    {testimonial.is_wall_of_love && <Badge>Wall of Love</Badge>}
                                    {testimonial.is_hidden && <Badge variant="secondary">Hidden</Badge>}
                                </div>
                                <div className="mt-3 flex flex-wrap gap-2">
                                    <Button size="sm" variant="outline" onClick={() => toggle(testimonial, 'is_favorite')}>
                                        <Heart className={testimonial.is_favorite ? 'fill-red-500 text-red-500' : ''} />
                                        {testimonial.is_favorite ? 'Unfavorite' : 'Favorite'}
                                    </Button>
                                    <Button
                                        size="sm"
                                        variant="outline"
                                        disabled={!testimonial.consent_given && !testimonial.is_wall_of_love}
                                        title={
                                            testimonial.consent_given
                                                ? undefined
                                                : 'Consent is required to add to the Wall of Love'
                                        }
                                        onClick={() => toggle(testimonial, 'is_wall_of_love')}
                                    >
                                        {testimonial.is_wall_of_love ? 'Remove from Wall' : 'Add to Wall of Love'}
                                    </Button>
                                    <Button size="sm" variant="outline" onClick={() => toggle(testimonial, 'is_hidden')}>
                                        {testimonial.is_hidden ? 'Unhide' : 'Hide'}
                                    </Button>
                                    <Button size="sm" variant="ghost" className="text-destructive" onClick={() => setDeleting(testimonial)}>
                                        <Trash2 /> Delete
                                    </Button>
                                </div>
                            </article>
                        ))}
                    </div>
                )}

                {testimonials.links.length > 3 && (
                    <nav className="flex flex-wrap gap-1">
                        {testimonials.links.map((link, position) =>
                            link.url ? (
                                <Button key={position} asChild size="sm" variant={link.active ? 'default' : 'outline'}>
                                    <Link href={link.url} dangerouslySetInnerHTML={{ __html: link.label }} />
                                </Button>
                            ) : null,
                        )}
                    </nav>
                )}
            </div>

            <Dialog open={deleting !== null} onOpenChange={(open) => !open && setDeleting(null)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Delete testimonial?</DialogTitle>
                        <DialogDescription>
                            The testimonial from {deleting?.submitter_name} will be permanently removed.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button variant="outline" onClick={() => setDeleting(null)}>
                            Cancel
                        </Button>
                        <Button
                            variant="destructive"
                            onClick={() => {
                                if (deleting) {
                                    router.delete(destroy.url({ space: space.id, testimonial: deleting.id }), {
                                        preserveScroll: true,
                                        onSuccess: () => setDeleting(null),
                                    });
                                }
                            }}
                        >
                            Delete
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </>
    );
}

SpaceTestimonials.layout = {
    breadcrumbs: [{ title: 'Spaces', href: spacesIndex() }],
};
