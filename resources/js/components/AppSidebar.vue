<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { Link, usePage } from '@inertiajs/vue3';
import { Bell, BookOpen, Calculator, Folder, LayoutGrid } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage();
const user = (page.props as unknown as { auth: { user: any } }).auth.user;

type MainNavItem = {
    title: string;
    href: string;
    icon: any;
    group?: string;
    show?: boolean;
    count?: number;
};

const mainNavItems: MainNavItem[] = [
    { title: 'Dashboard', href: route('dashboard'), icon: LayoutGrid, group: 'Main', show: true },
    { title: 'POS Terminal', href: route('pos.index'), icon: Calculator, group: 'Main', show: true },
    { title: 'Notifications', href: route('notifications.index'), icon: Bell, group: 'Main', show: true },
    { title: 'Users', href: route('users.index'), icon: Calculator, group: 'Admin', show: true },
    { title: 'Roles', href: route('roles.index'), icon: Folder, group: 'Admin', show: true },
    { title: 'Permissions', href: route('permissions.index'), icon: BookOpen, group: 'Admin', show: true },
    { title: 'Products', href: route('products.index'), icon: BookOpen, group: 'Main', show: true },
    { title: 'Orders', href: route('orders.index'), icon: BookOpen, group: 'Main', show: true },
    { title: 'Tables', href: route('tables.index'), icon: BookOpen, group: 'Restaurant', show: true },
].filter((item) => item.show === undefined || item.show);

const groupedNavItems = computed(() => {
    return mainNavItems.reduce((acc: Record<string, MainNavItem[]>, item) => {
        const group = item.group ?? 'Other';
        if (!acc[group]) acc[group] = [];
        acc[group].push(item);
        return acc;
    }, {});
});
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
            <template v-for="(items, group) in groupedNavItems" :key="group">
                <h3 class="px-4 pt-2 pb-1 text-xs font-semibold text-gray-500 uppercase">
                    {{ group }}
                </h3>
                <NavMain :items="items" />
            </template>
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
