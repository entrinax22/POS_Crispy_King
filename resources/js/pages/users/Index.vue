<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Statistics Summary Row -->
            <div class="grid gap-4 md:grid-cols-3">
                <div v-for="stat in stats" :key="stat.label" class="flex items-center gap-4 rounded-xl bg-white dark:bg-sidebar border border-sidebar-border/70 p-4 shadow-sm">
                    <span class="text-3xl">{{ stat.icon }}</span>
                    <div>
                        <div class="text-xl font-bold">{{ stat.value }}</div>
                        <div class="text-sm text-gray-500">{{ stat.label }}</div>
                    </div>
                </div>
            </div>
            <!-- Main Widgets -->
            <div class="grid gap-4 md:grid-cols-3">
                <!-- Sales Summary -->
                <div class="rounded-xl bg-white dark:bg-sidebar border border-sidebar-border/70 p-4 shadow-sm flex flex-col">
                    <div class="font-semibold mb-2">Sales Summary</div>
                    <div class="flex-1 flex flex-col justify-center items-center">
                        <div class="text-4xl font-bold text-green-600">₱120,000</div>
                        <div class="text-sm text-gray-500">This Month</div>
                    </div>
                </div>
                <!-- Top Products -->
                <div class="rounded-xl bg-white dark:bg-sidebar border border-sidebar-border/70 p-4 shadow-sm flex flex-col">
                    <div class="font-semibold mb-2">Top Products</div>
                    <ul>
                        <li v-for="product in topProducts" :key="product.name" class="flex justify-between py-1">
                            <span>{{ product.name }}</span>
                            <span class="font-mono">{{ product.sold }}</span>
                        </li>
                    </ul>
                </div>
                <!-- Recent Orders -->
                <div class="rounded-xl bg-white dark:bg-sidebar border border-sidebar-border/70 p-4 shadow-sm flex flex-col">
                    <div class="font-semibold mb-2">Recent Orders</div>
                    <ul>
                        <li v-for="order in recentOrders" :key="order.id" class="flex justify-between py-1 text-sm">
                            <span>#{{ order.id }} - {{ order.customer }}</span>
                            <span>{{ order.total }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Charts Section -->
            <div class="grid gap-4 md:grid-cols-2 mt-4">
                <div class="rounded-xl bg-white dark:bg-sidebar border border-sidebar-border/70 p-4 shadow-sm">
                    <Line :data="salesTrendData" :options="salesTrendOptions" />
                </div>
                <div class="rounded-xl bg-white dark:bg-sidebar border border-sidebar-border/70 p-4 shadow-sm">
                    <Bar :data="topProductsChartData" :options="topProductsChartOptions" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
