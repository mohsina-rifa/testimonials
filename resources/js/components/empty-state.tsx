import type { LucideIcon } from 'lucide-react';
import type { ReactNode } from 'react';

export default function EmptyState({
    icon: Icon,
    title,
    description,
    children,
}: {
    icon: LucideIcon;
    title: string;
    description: string;
    children?: ReactNode;
}) {
    return (
        <div className="flex flex-col items-center gap-3 rounded-xl border border-dashed p-10 text-center">
            <div className="rounded-full bg-muted p-3">
                <Icon className="size-6 text-muted-foreground" />
            </div>
            <h3 className="font-semibold">{title}</h3>
            <p className="max-w-sm text-sm text-muted-foreground">
                {description}
            </p>
            {children}
        </div>
    );
}
