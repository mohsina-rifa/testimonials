import { Head, useForm } from '@inertiajs/react';
import { CheckCircle2, Star } from 'lucide-react';
import InputError from '@/components/input-error';
import Textarea from '@/components/textarea';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import { store } from '@/routes/collection';

type FieldConfig = { enabled: boolean; required: boolean };

type Space = {
    title: string;
    subtitle: string;
    ask: string;
    slug: string;
    theme: 'light' | 'dark' | 'minimal';
    rating_enabled: boolean;
    fields: Record<string, FieldConfig>;
};

const themes = {
    light: 'bg-neutral-50 text-neutral-900',
    dark: 'bg-neutral-950 text-neutral-100',
    minimal: 'bg-white text-neutral-900',
};

export default function CollectionShow({
    space,
    unavailable,
    submitted,
}: {
    space: Space;
    unavailable: boolean;
    submitted: boolean;
}) {
    const form = useForm<{
        submitter_name: string;
        submitter_email: string;
        testimonial_text: string;
        rating: number | null;
        consent_given: boolean;
        company_name: string;
        social_link: string;
        profile_photo: File | null;
    }>({
        submitter_name: '',
        submitter_email: '',
        testimonial_text: '',
        rating: null,
        consent_given: false,
        company_name: '',
        social_link: '',
        profile_photo: null,
    });

    const requiredMark = (key: string) =>
        space.fields[key]?.required ? ' *' : ' (optional)';

    return (
        <>
            <Head title={space.title} />
            <div
                className={cn(
                    'flex min-h-screen items-start justify-center p-4 py-12',
                    themes[space.theme],
                )}
            >
                <div className="w-full max-w-lg space-y-6">
                    <header className="text-center">
                        <h1 className="text-3xl font-bold">{space.title}</h1>
                        <p className="mt-1 opacity-70">{space.subtitle}</p>
                    </header>

                    {submitted ? (
                        <div className="rounded-xl border p-8 text-center">
                            <CheckCircle2 className="mx-auto mb-3 size-10 text-green-600" />
                            <h2 className="text-xl font-semibold">
                                Thank you!
                            </h2>
                            <p className="mt-1 opacity-70">
                                Your testimonial has been received.
                            </p>
                        </div>
                    ) : unavailable ? (
                        <div className="rounded-xl border p-8 text-center">
                            <h2 className="text-xl font-semibold">
                                Not accepting testimonials right now
                            </h2>
                            <p className="mt-1 opacity-70">
                                Please check back later. Thanks for your
                                interest!
                            </p>
                        </div>
                    ) : (
                        <form
                            className="grid gap-4 rounded-xl border p-6"
                            onSubmit={(event) => {
                                event.preventDefault();
                                form.post(store.url(space.slug), {
                                    forceFormData: true,
                                });
                            }}
                        >
                            <p className="font-medium">{space.ask}</p>

                            <div className="grid gap-2">
                                <Label htmlFor="submitter_name">Name *</Label>
                                <Input
                                    id="submitter_name"
                                    value={form.data.submitter_name}
                                    onChange={(e) =>
                                        form.setData(
                                            'submitter_name',
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputError
                                    message={form.errors.submitter_name}
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="submitter_email">Email *</Label>
                                <Input
                                    id="submitter_email"
                                    type="email"
                                    value={form.data.submitter_email}
                                    onChange={(e) =>
                                        form.setData(
                                            'submitter_email',
                                            e.target.value,
                                        )
                                    }
                                />
                                <p className="text-xs opacity-60">
                                    Your email is never shown publicly.
                                </p>
                                <InputError
                                    message={form.errors.submitter_email}
                                />
                            </div>

                            {space.rating_enabled && (
                                <div className="grid gap-2">
                                    <Label>Rating *</Label>
                                    <div className="flex gap-1">
                                        {[1, 2, 3, 4, 5].map((star) => (
                                            <button
                                                key={star}
                                                type="button"
                                                aria-label={`${star} star${star > 1 ? 's' : ''}`}
                                                onClick={() =>
                                                    form.setData('rating', star)
                                                }
                                            >
                                                <Star
                                                    className={cn(
                                                        'size-7',
                                                        (form.data.rating ??
                                                            0) >= star
                                                            ? 'fill-amber-400 text-amber-400'
                                                            : 'text-neutral-400',
                                                    )}
                                                />
                                            </button>
                                        ))}
                                    </div>
                                    <InputError message={form.errors.rating} />
                                </div>
                            )}

                            <div className="grid gap-2">
                                <Label htmlFor="testimonial_text">
                                    Your testimonial *
                                </Label>
                                <Textarea
                                    id="testimonial_text"
                                    value={form.data.testimonial_text}
                                    onChange={(e) =>
                                        form.setData(
                                            'testimonial_text',
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputError
                                    message={form.errors.testimonial_text}
                                />
                            </div>

                            {space.fields.company?.enabled && (
                                <div className="grid gap-2">
                                    <Label htmlFor="company_name">
                                        Company{requiredMark('company')}
                                    </Label>
                                    <Input
                                        id="company_name"
                                        value={form.data.company_name}
                                        onChange={(e) =>
                                            form.setData(
                                                'company_name',
                                                e.target.value,
                                            )
                                        }
                                    />
                                    <InputError
                                        message={form.errors.company_name}
                                    />
                                </div>
                            )}
                            {space.fields.social_link?.enabled && (
                                <div className="grid gap-2">
                                    <Label htmlFor="social_link">
                                        Social link{requiredMark('social_link')}
                                    </Label>
                                    <Input
                                        id="social_link"
                                        type="url"
                                        placeholder="https://"
                                        value={form.data.social_link}
                                        onChange={(e) =>
                                            form.setData(
                                                'social_link',
                                                e.target.value,
                                            )
                                        }
                                    />
                                    <InputError
                                        message={form.errors.social_link}
                                    />
                                </div>
                            )}
                            {space.fields.profile_photo?.enabled && (
                                <div className="grid gap-2">
                                    <Label htmlFor="profile_photo">
                                        Profile photo
                                        {requiredMark('profile_photo')}
                                    </Label>
                                    <Input
                                        id="profile_photo"
                                        type="file"
                                        accept="image/*"
                                        onChange={(e) =>
                                            form.setData(
                                                'profile_photo',
                                                e.target.files?.[0] ?? null,
                                            )
                                        }
                                    />
                                    <InputError
                                        message={form.errors.profile_photo}
                                    />
                                </div>
                            )}

                            <div className="grid gap-2">
                                <div className="flex items-start gap-2">
                                    <Checkbox
                                        id="consent_given"
                                        checked={form.data.consent_given}
                                        onCheckedChange={(checked) =>
                                            form.setData(
                                                'consent_given',
                                                checked === true,
                                            )
                                        }
                                    />
                                    <Label
                                        htmlFor="consent_given"
                                        className="leading-snug"
                                    >
                                        I agree that my testimonial may be
                                        displayed publicly.
                                    </Label>
                                </div>
                                <InputError
                                    message={form.errors.consent_given}
                                />
                            </div>

                            <Button type="submit" disabled={form.processing}>
                                Submit testimonial
                            </Button>
                        </form>
                    )}
                </div>
            </div>
        </>
    );
}
