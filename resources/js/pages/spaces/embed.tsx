import { Head, useForm } from '@inertiajs/react';
import { Copy } from 'lucide-react';
import { toast } from 'sonner';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import WallOfLove from '@/components/wall-of-love';
import type { EmbedConfiguration, PublicTestimonial } from '@/components/wall-of-love';
import { index as spacesIndex } from '@/routes/spaces';
import { update } from '@/routes/spaces/embed';

const toggles = [
    { key: 'dark_mode', label: 'Dark mode' },
    { key: 'animation_enabled', label: 'Animation' },
    { key: 'show_rating', label: 'Show rating' },
    { key: 'show_company', label: 'Show company' },
    { key: 'show_profile_photo', label: 'Show profile photo' },
] as const;

export default function SpaceEmbed({
    space,
    configuration,
    testimonials,
    embed_url,
}: {
    space: { id: number; title: string };
    configuration: EmbedConfiguration;
    testimonials: PublicTestimonial[];
    embed_url: string;
}) {
    const form = useForm<EmbedConfiguration>(configuration);
    const snippet = `<iframe src="${embed_url}" width="100%" height="600" style="border:0" title="Testimonials"></iframe>`;

    return (
        <>
            <Head title={`${space.title} embed`} />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <h1 className="text-2xl font-semibold">{space.title}</h1>

                <div className="grid gap-6 lg:grid-cols-[20rem_1fr]">
                    <form
                        className="grid content-start gap-4"
                        onSubmit={(event) => {
                            event.preventDefault();
                            form.put(update.url(space.id));
                        }}
                    >
                        <div className="grid gap-2">
                            <Label>Layout</Label>
                            <Select
                                value={form.data.layout}
                                onValueChange={(value) => form.setData('layout', value as EmbedConfiguration['layout'])}
                            >
                                <SelectTrigger className="w-full">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="masonry">Masonry</SelectItem>
                                    <SelectItem value="carousel">Carousel</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError message={form.errors.layout} />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="background_color">Background color</Label>
                            <div className="flex gap-2">
                                <input
                                    type="color"
                                    aria-label="Pick background color"
                                    className="h-9 w-12 rounded border"
                                    value={/^#[0-9a-fA-F]{6}$/.test(form.data.background_color) ? form.data.background_color : '#ffffff'}
                                    onChange={(e) => form.setData('background_color', e.target.value)}
                                />
                                <Input
                                    id="background_color"
                                    value={form.data.background_color}
                                    onChange={(e) => form.setData('background_color', e.target.value)}
                                />
                            </div>
                            <InputError message={form.errors.background_color} />
                        </div>
                        {toggles.map((toggle) => (
                            <div key={toggle.key} className="flex items-center gap-2">
                                <Checkbox
                                    id={toggle.key}
                                    checked={form.data[toggle.key]}
                                    onCheckedChange={(checked) => form.setData(toggle.key, checked === true)}
                                />
                                <Label htmlFor={toggle.key}>{toggle.label}</Label>
                            </div>
                        ))}
                        <Button type="submit" disabled={form.processing}>
                            Save settings
                        </Button>
                    </form>

                    <div className="min-w-0 space-y-2">
                        <h2 className="font-semibold">Preview</h2>
                        <div className="overflow-hidden rounded-xl border">
                            <WallOfLove testimonials={testimonials} configuration={form.data} />
                        </div>
                    </div>
                </div>

                <div className="max-w-3xl space-y-2">
                    <h2 className="font-semibold">Embed code</h2>
                    <pre className="overflow-x-auto rounded-lg bg-muted p-3 text-xs">{snippet}</pre>
                    <div className="flex items-center gap-3">
                        <Button
                            variant="outline"
                            size="sm"
                            onClick={() => {
                                void navigator.clipboard?.writeText(snippet);
                                toast.success('Embed code copied');
                            }}
                        >
                            <Copy /> Copy code
                        </Button>
                        <a className="text-sm text-primary underline" href={embed_url} target="_blank" rel="noreferrer">
                            Open public wall
                        </a>
                    </div>
                </div>
            </div>
        </>
    );
}

SpaceEmbed.layout = {
    breadcrumbs: [{ title: 'Spaces', href: spacesIndex() }],
};
