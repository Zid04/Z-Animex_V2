import { Link } from '@inertiajs/react';
import { LayoutGrid, Film, ListChecks, Heart, Star } from 'lucide-react';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';

import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

/*
|--------------------------------------------------------------------------
| NAVIGATION ITEMS (Animex)
|--------------------------------------------------------------------------
*/

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Médias',
        href: '/media',
        icon: Film,
    },
    {
        title: 'Mes médias',
        href: '/my-media',
        icon: Heart,
    },
    {
        title: 'Favoris',
        href: '/favorites',
        icon: Star,
    },
    {
        title: 'Watchlist',
        href: '/watchlist',
        icon: ListChecks,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            {/* LOGO */}
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <img
                                    src="/Images/logo.png"
                                    alt="Z-Animex"
                                    className="h-10 w-auto"
                                />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            {/* MAIN NAVIGATION */}
            <SidebarContent>
                <SidebarMenu className="px-2 py-4">
                    {mainNavItems.map((item) => (
                        <SidebarMenuItem key={item.title}>
                            <SidebarMenuButton asChild tooltip={item.title}>
                                <Link href={item.href} prefetch>
                                    {item.icon && <item.icon />}
                                    <span>{item.title}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    ))}
                </SidebarMenu>
            </SidebarContent>

            {/* FOOTER */}
            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
