<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { Link, usePage } from '@inertiajs/vue3';
import { Home, KeyRound, LayoutDashboard, Package, ReceiptText, ShieldHalf, ShoppingCart, Users, UtensilsCrossed } from 'lucide-vue-next';
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
    { title: 'Dashboard', href: route('dashboard'), icon: LayoutDashboard, group: 'Main' },
    { title: 'POS Terminal', href: route('pos.index'), icon: ShoppingCart, group: 'Main' },
    { title: 'Homepage', href: route('customers.index'), icon: Home, group: 'Main' },
    { title: 'Users', href: route('users.index'), icon: Users, group: 'Admin' },
    { title: 'Roles', href: route('roles.index'), icon: ShieldHalf, group: 'Admin' },
    { title: 'Permissions', href: route('permissions.index'), icon: KeyRound, group: 'Admin' },
    { title: 'Products', href: route('products.index'), icon: Package, group: 'Main' },
    { title: 'Orders', href: route('orders.index'), icon: ReceiptText, group: 'Main' },
    { title: 'Tables', href: route('tables.index'), icon: UtensilsCrossed, group: 'Restaurant' },
];

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
