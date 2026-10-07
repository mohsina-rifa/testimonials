import { usePage } from '@inertiajs/react';
import { CheckCircle2, CircleAlert } from 'lucide-react';
import { Alert, AlertDescription } from '@/components/ui/alert';

type Flash = { success?: string | null; error?: string | null };

export default function FlashMessages() {
    const { flash } = usePage<{ flash?: Flash }>().props;

    if (!flash?.success && !flash?.error) {
        return null;
    }

    return (
        <div className="space-y-2 px-4 pt-4" role="status">
            {flash.success && (
                <Alert className="border-green-200 bg-green-50 text-green-900 dark:border-green-900 dark:bg-green-950 dark:text-green-100">
                    <CheckCircle2 className="text-green-600" />
                    <AlertDescription className="text-inherit">
                        {flash.success}
                    </AlertDescription>
                </Alert>
            )}
            {flash.error && (
                <Alert variant="destructive">
                    <CircleAlert />
                    <AlertDescription>{flash.error}</AlertDescription>
                </Alert>
            )}
        </div>
    );
}
