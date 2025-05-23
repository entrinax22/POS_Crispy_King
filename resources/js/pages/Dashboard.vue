<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Line, Bar } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    BarElement,
    CategoryScale,
    LinearScale,
    PointElement,
} from 'chart.js';

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    LineElement,
    BarElement,
    CategoryScale,
    LinearScale,
    PointElement
);

// Mock data for dashboard widgets
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const stats = [
    { label: 'Total Sales', value: '₱120,000', icon: '💰' },
    { label: 'Orders Today', value: '85', icon: '🛒' },
    { label: 'Customers', value: '1,230', icon: '👥' },
];

const topProducts = [
    { name: 'Crispy Chicken', sold: 320 },
    { name: 'Spicy Wings', sold: 210 },
    { name: 'Burger Meal', sold: 180 },
];

const recentOrders = [
    { id: 1001, customer: 'Juan Dela Cruz', total: '₱350', time: '10:15 AM' },
    { id: 1002, customer: 'Maria Santos', total: '₱420', time: '10:22 AM' },
    { id: 1003, customer: 'Pedro Reyes', total: '₱150', time: '10:30 AM' },
];

// Mock data for charts
const salesTrendData = {
    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
    datasets: [
        {
            label: 'Sales (₱)',
            data: [18000, 22000, 20000, 25000, 27000, 30000, 32000],
            borderColor: '#22c55e',
            backgroundColor: 'rgba(34,197,94,0.1)',
            tension: 0.4,
            fill: true,
        },
    ],
};

const salesTrendOptions = {
    responsive: true,
    plugins: {
        legend: { display: false },
        title: { display: true, text: 'Sales Trend (This Week)' },
    },
    scales: {
        y: { beginAtZero: true },
    },
};

const topProductsChartData = {
    labels: topProducts.map(p => p.name),
    datasets: [
        {
            label: 'Units Sold',
            data: topProducts.map(p => p.sold),
            backgroundColor: ['#fbbf24', '#f87171', '#60a5fa'],
        },
    ],
};

const topProductsChartOptions = {
    responsive: true,
    plugins: {
        legend: { display: false },
        title: { display: true, text: 'Top Products' },
    },
    scales: {
        y: { beginAtZero: true },
    },
};
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
