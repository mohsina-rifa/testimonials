import { cn } from '@/lib/utils';
import StarRating from '@/components/star-rating';

export type EmbedConfiguration = {
    layout: 'masonry' | 'carousel';
    dark_mode: boolean;
    animation_enabled: boolean;
    background_color: string;
    show_rating: boolean;
    show_company: boolean;
    show_profile_photo: boolean;
};

export type PublicTestimonial = {
    id: number;
    name: string;
    company: string | null;
    text: string;
    rating: number | null;
    photo_url: string | null;
};

function Card({
    testimonial,
    configuration,
}: {
    testimonial: PublicTestimonial;
    configuration: EmbedConfiguration;
}) {
    return (
        <figure
            className={cn(
                'break-inside-avoid rounded-xl border p-4 shadow-sm',
                configuration.dark_mode
                    ? 'border-neutral-700 bg-neutral-900 text-neutral-100'
                    : 'border-neutral-200 bg-white text-neutral-900',
                configuration.animation_enabled &&
                    'transition-transform hover:-translate-y-1',
                configuration.layout === 'carousel' &&
                    'w-72 shrink-0 snap-start',
            )}
        >
            {configuration.show_rating && testimonial.rating !== null && (
                <StarRating value={testimonial.rating} />
            )}
            <blockquote className="mt-2 text-sm">{testimonial.text}</blockquote>
            <figcaption className="mt-3 flex items-center gap-2 text-sm">
                {configuration.show_profile_photo && testimonial.photo_url && (
                    <img
                        src={testimonial.photo_url}
                        alt=""
                        className="size-8 rounded-full object-cover"
                    />
                )}
                <div>
                    <div className="font-medium">{testimonial.name}</div>
                    {configuration.show_company && testimonial.company && (
                        <div className="text-xs opacity-70">
                            {testimonial.company}
                        </div>
                    )}
                </div>
            </figcaption>
        </figure>
    );
}

export default function WallOfLove({
    testimonials,
    configuration,
}: {
    testimonials: PublicTestimonial[];
    configuration: EmbedConfiguration;
}) {
    if (testimonials.length === 0) {
        return (
            <p className="p-8 text-center text-sm text-neutral-500">
                No testimonials have been shared here yet.
            </p>
        );
    }

    return (
        <div
            className={cn(
                'p-4',
                configuration.layout === 'carousel'
                    ? 'flex snap-x gap-4 overflow-x-auto'
                    : 'columns-1 gap-4 space-y-4 sm:columns-2 lg:columns-3',
            )}
            style={{ backgroundColor: configuration.background_color }}
        >
            {testimonials.map((testimonial) => (
                <Card
                    key={testimonial.id}
                    testimonial={testimonial}
                    configuration={configuration}
                />
            ))}
        </div>
    );
}
