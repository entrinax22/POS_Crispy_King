<template>
    <Head title="Crispy King | Notifications" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="mb-6 text-3xl font-bold text-gray-800">Notifications</h1>

            <div v-if="notifications.length" class="space-y-4">
                <div
                    v-for="notification in notifications"
                    :key="notification.id"
                    class="flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-md transition hover:shadow-lg"
                >
                    <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                        <!-- Notification bell icon -->
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                            />
                        </svg>
                    </div>

                    <div class="flex flex-1 flex-col">
                        <p class="text-sm leading-relaxed text-gray-800">
                            {{ notification.message }}
                        </p>

                        <div class="mt-2 flex flex-wrap items-center justify-between text-xs text-gray-500">
                            <span>{{ formatDate(notification.created_at) }}</span>

                            <button
                                v-if="notification.order_id"
                                @click="viewOrder(notification.order_id)"
                                class="inline-flex items-center gap-1 rounded-md bg-blue-100 px-3 py-1 text-xs font-medium text-blue-600 transition hover:bg-blue-200"
                            >
                                View Order
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="py-8 text-center text-sm text-gray-500">
                <svg class="mx-auto mb-2 h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.73 21a2 2 0 01-3.46 0M12 17v-1m0-4a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                You have no notifications.
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { useNotify } from '@/composables/useNotify';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { formatDistanceToNow } from 'date-fns';
import { onMounted, ref } from 'vue';

const notify = useNotify();
const notifications = ref<any[]>([]);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Orders',
        href: route('orders.index'),
    },
];

// Fetch notifications from API
const fetchNotifications = async () => {
    try {
        const response = await axios.get(route('notifications.list'));
        if (response.data.result) {
            notifications.value = response.data.data;
        } else {
            notify('Failed to load notifications', 'error');
        }
    } catch (err) {
        notify('Something went wrong', 'error');
    }
};

// Format timestamp
const formatDate = (date: string): string => {
    return formatDistanceToNow(new Date(date), { addSuffix: true });
};

// Handle clicking "View Order"
const viewOrder = (orderId: string | number) => {
    // If orderId is encrypted, it's still fine to use in route
    window.location.href = route('orders.show', orderId);
};

onMounted(() => {
    fetchNotifications();
});
</script>
