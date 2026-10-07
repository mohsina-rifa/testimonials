import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import SpaceForm from '@/components/space-form';
import type { SpaceFormData } from '@/components/space-form';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { destroy, index, update } from '@/routes/spaces';

type Space = SpaceFormData & { id: number; collection_url: string };

export default function SpacesEdit({ space }: { space: Space }) {
    const [confirming, setConfirming] = useState(false);

    return (
        <>
            <Head title={`${space.title} settings`} />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <h1 className="text-2xl font-semibold">{space.title}</h1>
                <p className="text-sm text-muted-foreground">
                    Public page:{' '}
                    <a className="text-primary underline" href={space.collection_url} target="_blank" rel="noreferrer">
                        {space.collection_url}
                    </a>
                </p>
                <SpaceForm
                    initial={space}
                    action={update.url(space.id)}
                    method="put"
                    submitLabel="Save changes"
                />

                <div className="max-w-2xl rounded-lg border border-destructive/40 p-4">
                    <h2 className="font-semibold text-destructive">Delete space</h2>
                    <p className="mt-1 text-sm text-muted-foreground">
                        This permanently removes the space and all of its testimonials.
                    </p>
                    <Button variant="destructive" className="mt-3" onClick={() => setConfirming(true)}>
                        Delete space
                    </Button>
                </div>
            </div>

            <Dialog open={confirming} onOpenChange={setConfirming}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Delete {space.title}?</DialogTitle>
                        <DialogDescription>
                            All testimonials in this space will be permanently removed. This cannot be undone.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button variant="outline" onClick={() => setConfirming(false)}>
                            Cancel
                        </Button>
                        <Button variant="destructive" onClick={() => router.delete(destroy.url(space.id))}>
                            Delete
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </>
    );
}

SpacesEdit.layout = {
    breadcrumbs: [{ title: 'Spaces', href: index() }],
};
