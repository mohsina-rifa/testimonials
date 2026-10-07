import { router, usePage } from '@inertiajs/react';
import { ChevronsUpDown, FolderKanban } from 'lucide-react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/hooks/use-current-url';

export function SpaceSwitcher({ currentSpaceId }: { currentSpaceId: number | null }) {
    const { ownerSpaces } = usePage().props;
    const { currentUrl } = useCurrentUrl();
    const current = ownerSpaces.find((space) => space.id === currentSpaceId);

    const switchTo = (spaceId: number) => {
        const section = currentUrl.match(/^\/spaces\/[^/]+(\/.*)?$/)?.[1];
        const sectionPath = section && section !== '/' ? section : '/dashboard';

        router.visit(`/spaces/${spaceId}${sectionPath}`);
    };

    return (
        <SidebarMenu>
            <SidebarMenuItem>
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <SidebarMenuButton data-test="space-switcher">
                            <FolderKanban />
                            <span className="truncate">{current?.title ?? 'Select a space'}</span>
                            <ChevronsUpDown className="ml-auto size-4" />
                        </SidebarMenuButton>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                        className="w-(--radix-dropdown-menu-trigger-width) min-w-56"
                        align="start"
                    >
                        <DropdownMenuLabel>Your spaces</DropdownMenuLabel>
                        {ownerSpaces.map((space) => (
                            <DropdownMenuItem key={space.id} onSelect={() => switchTo(space.id)}>
                                {space.title}
                            </DropdownMenuItem>
                        ))}
                        {ownerSpaces.length === 0 && (
                            <DropdownMenuItem disabled>No spaces yet</DropdownMenuItem>
                        )}
                    </DropdownMenuContent>
                </DropdownMenu>
            </SidebarMenuItem>
        </SidebarMenu>
    );
}
