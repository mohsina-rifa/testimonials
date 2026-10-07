import { Head } from '@inertiajs/react';
import WallOfLove from '@/components/wall-of-love';
import type {
    EmbedConfiguration,
    PublicTestimonial,
} from '@/components/wall-of-love';

export default function EmbedShow({
    title,
    configuration,
    testimonials,
}: {
    title: string;
    configuration: EmbedConfiguration;
    testimonials: PublicTestimonial[];
}) {
    return (
        <>
            <Head title={`${title} – Wall of Love`} />
            <div
                className="min-h-screen"
                style={{ backgroundColor: configuration.background_color }}
            >
                <WallOfLove
                    testimonials={testimonials}
                    configuration={configuration}
                />
            </div>
        </>
    );
}
