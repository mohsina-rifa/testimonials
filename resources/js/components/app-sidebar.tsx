import { Link, usePage } from '@inertiajs/react';
import {
    CreditCard,
    FolderKanban,
    Inbox,
    LayoutDashboard,
    Settings,
    SlidersHorizontal,
    Code,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { SpaceSwitcher } from '@/components/space-switcher';
import { ThemeToggle } from '@/components/theme-toggle';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { index as billing } from '@/routes/billing';
import { edit as settings } from '@/routes/profile';
import {
    dashboard as spaceDashboard,
    edit as editSpace,
    index as spaces,
} from '@/routes/spaces';
import { edit as editEmbed } from '@/routes/spaces/embed';
import { index as inbox } from '@/routes/spaces/testimonials';
import type { NavItem } from '@/types';

const globalNavItems: NavItem[] = [
    { title: 'Spaces', href: spaces(), icon: FolderKanban },
    { title: 'Billing', href: billing(), icon: CreditCard },
    { title: 'Settings', href: settings(), icon: Settings },
];

export function AppSidebar() {
    const { space } = usePage<{ space?: { id: number } }>().props;
    const spaceId = space?.id ?? null;

    const spaceNavItems: NavItem[] =
        spaceId === null
            ? []
            : [
                  {
                      title: 'Dashboard',
                      href: spaceDashboard(spaceId),
                      icon: LayoutDashboard,
                  },
                  { title: 'Inbox', href: inbox(spaceId), icon: Inbox },
                  { title: 'Embed', href: editEmbed(spaceId), icon: Code },
                  {
                      title: 'Space',
                      href: editSpace(spaceId),
                      icon: SlidersHorizontal,
                  },
              ];

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={spaces()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
                <SpaceSwitcher currentSpaceId={spaceId} />
            </SidebarHeader>

            <SidebarContent>
                {spaceNavItems.length > 0 && (
                    <NavMain label="Space" items={spaceNavItems} />
                )}
                <NavMain label="Account" items={globalNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <ThemeToggle />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
