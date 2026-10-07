import { useForm } from '@inertiajs/react';
import InputError from '@/components/input-error';
import Textarea from '@/components/textarea';
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

export type SpaceFormData = {
    title: string;
    subtitle: string;
    ask: string;
    slug: string;
    theme: string;
    rating_enabled: boolean;
    field_configuration: Record<string, { enabled: boolean; required: boolean }>;
};

const optionalFields = [
    { key: 'company', label: 'Company' },
    { key: 'social_link', label: 'Social link' },
    { key: 'profile_photo', label: 'Profile photo' },
];

export default function SpaceForm({
    initial,
    action,
    method,
    disabled = false,
    submitLabel,
}: {
    initial: SpaceFormData;
    action: string;
    method: 'post' | 'put';
    disabled?: boolean;
    submitLabel: string;
}) {
    const form = useForm<SpaceFormData>(initial);
    const errors = form.errors as Record<string, string | undefined>;

    const setField = (key: string, part: 'enabled' | 'required', value: boolean) =>
        form.setData('field_configuration', {
            ...form.data.field_configuration,
            [key]: {
                ...form.data.field_configuration[key],
                [part]: value,
                ...(part === 'enabled' && !value ? { required: false } : {}),
            },
        });

    return (
        <form
            className="grid max-w-2xl gap-6"
            onSubmit={(event) => {
                event.preventDefault();
                form[method](action);
            }}
        >
            <div className="grid gap-2">
                <Label htmlFor="title">Title</Label>
                <Input id="title" value={form.data.title} onChange={(e) => form.setData('title', e.target.value)} />
                <InputError message={form.errors.title} />
            </div>
            <div className="grid gap-2">
                <Label htmlFor="subtitle">Subtitle</Label>
                <Input id="subtitle" value={form.data.subtitle} onChange={(e) => form.setData('subtitle', e.target.value)} />
                <InputError message={form.errors.subtitle} />
            </div>
            <div className="grid gap-2">
                <Label htmlFor="ask">Question for your customers</Label>
                <Textarea id="ask" value={form.data.ask} onChange={(e) => form.setData('ask', e.target.value)} />
                <InputError message={form.errors.ask} />
            </div>
            <div className="grid gap-2">
                <Label htmlFor="slug">Slug</Label>
                <Input id="slug" value={form.data.slug} onChange={(e) => form.setData('slug', e.target.value.toLowerCase())} />
                <p className="text-xs text-muted-foreground">Used in your public link: /s/{form.data.slug || 'your-slug'}</p>
                <InputError message={form.errors.slug} />
            </div>
            <div className="grid gap-2">
                <Label>Theme</Label>
                <Select value={form.data.theme} onValueChange={(value) => form.setData('theme', value)}>
                    <SelectTrigger className="w-48">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="light">Light</SelectItem>
                        <SelectItem value="dark">Dark</SelectItem>
                        <SelectItem value="minimal">Minimal</SelectItem>
                    </SelectContent>
                </Select>
                <InputError message={form.errors.theme} />
            </div>

            <fieldset className="grid gap-3 rounded-lg border p-4">
                <legend className="px-1 text-sm font-medium">Form fields</legend>
                <p className="text-xs text-muted-foreground">Name, email, testimonial and consent are always collected.</p>
                <div className="flex items-center gap-2">
                    <Checkbox
                        id="rating_enabled"
                        checked={form.data.rating_enabled}
                        onCheckedChange={(checked) => form.setData('rating_enabled', checked === true)}
                    />
                    <Label htmlFor="rating_enabled">Ask for a star rating</Label>
                </div>
                {optionalFields.map((field) => {
                    const config = form.data.field_configuration[field.key];

                    return (
                        <div key={field.key} className="flex flex-wrap items-center gap-4">
                            <div className="flex items-center gap-2">
                                <Checkbox
                                    id={`${field.key}-enabled`}
                                    checked={config.enabled}
                                    onCheckedChange={(checked) => setField(field.key, 'enabled', checked === true)}
                                />
                                <Label htmlFor={`${field.key}-enabled`}>{field.label}</Label>
                            </div>
                            <div className="flex items-center gap-2">
                                <Checkbox
                                    id={`${field.key}-required`}
                                    checked={config.required}
                                    disabled={!config.enabled}
                                    onCheckedChange={(checked) => setField(field.key, 'required', checked === true)}
                                />
                                <Label htmlFor={`${field.key}-required`} className="text-muted-foreground">
                                    Required
                                </Label>
                            </div>
                        </div>
                    );
                })}
                <InputError message={errors['field_configuration']} />
            </fieldset>

            <div>
                <Button type="submit" disabled={disabled || form.processing}>
                    {submitLabel}
                </Button>
            </div>
        </form>
    );
}
