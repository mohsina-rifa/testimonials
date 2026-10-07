import { Link } from '@inertiajs/react';
import { Sparkles } from 'lucide-react';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { index } from '@/routes/billing';

export default function UpgradePrompt({ message }: { message: string }) {
    return (
        <Alert className="border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-100">
            <Sparkles className="text-amber-600" />
            <AlertTitle>Plan limit reached</AlertTitle>
            <AlertDescription className="text-inherit">
                <p>{message}</p>
                <Button asChild size="sm" className="mt-2">
                    <Link href={index()}>Upgrade to Pro</Link>
                </Button>
            </AlertDescription>
        </Alert>
    );
}
