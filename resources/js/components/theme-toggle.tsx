import { Moon, Sun } from 'lucide-react';
import { useEffect, useState } from 'react';
import { Button } from '@/components/ui/button';
import { useAppearance } from '@/hooks/use-appearance';

export function ThemeToggle() {
    const { resolvedAppearance, updateAppearance } = useAppearance();
    const [mounted, setMounted] = useState(false);

    useEffect(() => setMounted(true), []);

    // Match the server render until mounted to avoid a hydration mismatch.
    const isDark = mounted && resolvedAppearance === 'dark';
    const label = isDark ? 'Switch to light mode' : 'Switch to dark mode';

    return (
        <Button
            variant="ghost"
            size="icon"
            aria-label={label}
            title={label}
            onClick={() => updateAppearance(isDark ? 'light' : 'dark')}
            data-test="theme-toggle"
        >
            {isDark ? <Sun /> : <Moon />}
        </Button>
    );
}
