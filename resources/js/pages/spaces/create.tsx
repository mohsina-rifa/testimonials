import { Head } from '@inertiajs/react';
import SpaceForm from '@/components/space-form';
import UpgradePrompt from '@/components/upgrade-prompt';
import { create, index, store } from '@/routes/spaces';

export default function SpacesCreate({
    plan,
}: {
    plan: { max_spaces: number; spaces_used: number };
}) {
    const atLimit = plan.spaces_used >= plan.max_spaces;

    return (
        <>
            <Head title="New space" />
            <div className="flex flex-1 flex-col gap-4 p-4">
                <h1 className="text-2xl font-semibold">New space</h1>
                {atLimit && (
                    <UpgradePrompt message="You have reached the space limit for your plan." />
                )}
                <SpaceForm
                    initial={{
                        title: '',
                        subtitle: '',
                        ask: '',
                        slug: '',
                        theme: 'light',
                        rating_enabled: true,
                        field_configuration: {
                            company: { enabled: false, required: false },
                            social_link: { enabled: false, required: false },
                            profile_photo: { enabled: false, required: false },
                        },
                    }}
                    action={store.url()}
                    method="post"
                    disabled={atLimit}
                    submitLabel="Create space"
                />
            </div>
        </>
    );
}

SpacesCreate.layout = {
    breadcrumbs: [
        { title: 'Spaces', href: index() },
        { title: 'New space', href: create() },
    ],
};
