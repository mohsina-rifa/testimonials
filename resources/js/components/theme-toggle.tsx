import { Moon, Sun } from 'lucide-react';
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useAppearance } from '@/hooks/use-appearance';

export function ThemeToggle() {
    const { resolvedAppearance, updateAppearance } = useAppearance();
    const isDark = resolvedAppearance === 'dark';

    return (
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton
                    tooltip={{ children: isDark ? 'Light mode' : 'Dark mode' }}
                    onClick={() => updateAppearance(isDark ? 'light' : 'dark')}
                    data-test="theme-toggle"
                >
                    {isDark ? <Sun /> : <Moon />}
                    <span>{isDark ? 'Light mode' : 'Dark mode'}</span>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    );
}
