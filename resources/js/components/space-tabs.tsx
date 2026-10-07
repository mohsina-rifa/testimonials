import { Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';
import { edit } from '@/routes/spaces';
import { edit as editEmbed } from '@/routes/spaces/embed';
import { index as testimonials } from '@/routes/spaces/testimonials';

export default function SpaceTabs({
    space,
    active,
}: {
    space: { id: number; title: string };
    active: 'testimonials' | 'settings' | 'embed';
}) {
    const tabs = [
        { key: 'testimonials', label: 'Testimonials', href: testimonials(space.id) },
        { key: 'settings', label: 'Settings', href: edit(space.id) },
        { key: 'embed', label: 'Embed', href: editEmbed(space.id) },
    ] as const;

    return (
        <div className="space-y-4">
            <h1 className="text-2xl font-semibold">{space.title}</h1>
            <nav className="flex gap-1 border-b">
                {tabs.map((tab) => (
                    <Link
                        key={tab.key}
                        href={tab.href}
                        className={cn(
                            '-mb-px border-b-2 px-4 py-2 text-sm font-medium',
                            active === tab.key
                                ? 'border-primary text-primary'
                                : 'border-transparent text-muted-foreground hover:text-foreground',
                        )}
                    >
                        {tab.label}
                    </Link>
                ))}
            </nav>
        </div>
    );
}
