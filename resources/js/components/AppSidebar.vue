<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Calculator } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import {usePage} from '@inertiajs/vue3';

const page = usePage();
const user = ((page.props as unknown) as { auth: { user: any } }).auth.user;

type MainNavItem = {
    title: string;
    href: string;
    icon: any;
    show?: boolean;
};

const mainNavItems: MainNavItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
        icon: LayoutGrid,
        show: true,
    },
    {
        title: 'POS Terminal',
        href: route('pos.index'),
        icon: Calculator,
        show: true,
    },
    {
        title: 'Users',
        href: route('users.index'),
        icon: Calculator,
        show: true,
    },
    {
        title: 'Roles',
        href: route('roles.index'), 
        icon: Folder,
        show: true,
    },
    {
        title: 'Permissions',
        href: route('permissions.index'),
        icon: BookOpen,
        show: true,
    },
    // {
    //     title: 'Products',
    //     href: route('products.index'),
    //     icon: BookOpen,
    //     show: true,
    // }
].filter(item => item.show === undefined || item.show);

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
