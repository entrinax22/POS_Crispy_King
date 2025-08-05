<script setup lang="ts">
import { SidebarGroup, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';
defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();
const unreadCount = ref(0);

const fecthUnreadNotificationsCount = async () => {
    try {
        const response = await axios.get(route('notifications.unreadCount'));
        if (response.data.result) {
            unreadCount.value = response.data.count;
        } else {
            console.error('Failed to fetch unread notifications count:', response.data.message);
        }
    } catch (error) {
        console.error('Error fetching unread notifications count:', error);
    }
};

onMounted(() => {
    fecthUnreadNotificationsCount();
});
</script>
<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton as-child :is-active="item.href === page.url" :tooltip="item.title">
                    <Link :href="item.href" class="flex w-full items-center justify-between">
                        <div class="flex items-center gap-2">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </div>
                        <!-- Badge -->
                        <span
                            v-if="item.title === 'Notifications' && unreadCount > 0"
                            class="ml-2 inline-flex items-center justify-center rounded-full bg-red-500 px-2 py-0.5 text-xs font-bold text-white"
                        >
                            {{ unreadCount }}
                        </span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
