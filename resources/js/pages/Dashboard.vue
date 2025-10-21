<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { BarElement, CategoryScale, Chart as ChartJS, Filler, Legend, LinearScale, LineElement, PointElement, Title, Tooltip } from 'chart.js';
import { ref } from 'vue';
import { Bar, Line } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, LineElement, BarElement, CategoryScale, LinearScale, PointElement, Filler);

defineProps<{
    stats: { totalSales: number; ordersToday: number; customers: number };
    topProducts: { name: string; sold: number }[];
    recentOrders: { id: number; customer: string; total: string; time: string }[];
    salesTrend: { labels: string[]; data: number[] };
}>();

const breadcrumbs = [{ title: 'Dashboard', href: '/dashboard' }];
const generatedReportData = ref<any>(null);

const generateReport = async () => {
    const route_url = route('dashboard.generateReport');
    try {
        const response = await axios.get(route_url);
        if (response.data.result === true) {
            generatedReportData.value = response.data.data;
            printReport(generatedReportData.value);
        }
    } catch (error) {
        console.log(error);
    }
};

const printReport = (data: any) => {
    let ordersHtml = '';
    data.today_orders_list.forEach((order: any) => {
        let itemsHtml = '';
        order.order_items.forEach((item: any) => {
            itemsHtml += `<li>${item.product.product_name} x${item.quantity} (₱${item.total})</li>`;
        });

        ordersHtml += `
            <tr>
                <td>#${order.order_id}</td>
                <td>₱${order.total_amount}</td>
                <td>${order.status}</td>
                <td><ul>${itemsHtml}</ul></td>
            </tr>
        `;
    });

    const reportHTML = `
        <html>
        <head>
            <title>Daily Sales Report - ${data.date}</title>
            <style>
                body { 
                    font-family: 'Segoe UI', Arial, sans-serif; 
                    padding: 30px; 
                    background-color: #f9fafb; 
                    color: #111827; 
                }
                header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                header h1 {
                    font-size: 28px;
                    margin: 0;
                    color: #dc2626;
                    text-transform: uppercase;
                }
                header h2 {
                    font-size: 16px;
                    font-weight: normal;
                    margin: 5px 0 0;
                    color: #4b5563;
                }
                table { 
                    border-collapse: collapse; 
                    width: 100%; 
                    margin-top: 15px; 
                    background: white; 
                }
                table, th, td { 
                    border: 1px solid #d1d5db; 
                }
                th { 
                    background-color: #dc2626; 
                    color: white; 
                    padding: 10px; 
                    text-align: left;
                }
                td { 
                    padding: 8px; 
                    font-size: 14px; 
                }
                tr:nth-child(even) { 
                    background-color: #f3f4f6; 
                }
                .summary { 
                    margin: 20px 0; 
                    padding: 15px; 
                    background: #fff; 
                    border: 1px solid #e5e7eb; 
                    border-radius: 8px;
                }
                .summary p { 
                    margin: 6px 0; 
                }
                footer {
                    margin-top: 30px;
                    text-align: center;
                    font-size: 12px;
                    color: #6b7280;
                }
            </style>
        </head>
        <body>
            <header>
                <h1>🍗 Crispy King</h1>
                <h2>Daily Sales Report</h2>
                <p><b>Date:</b> ${data.date}</p>
            </header>

            <div class="summary">
                <p><b>Total Orders Today:</b> ${data.total_orders_today}</p>
                <p><b>Sales Today:</b> ₱${data.sales_today}</p>
                <p><b>Orders This Month:</b> ${data.orders_this_month}</p>
                <p><b>Sales This Month:</b> ₱${data.sales_this_month}</p>
                <p><b>Popular Product of the Month:</b> ${data.popular_product_this_month?.product_name || 'N/A'} 
                (Qty: ${data.popular_product_this_month?.total_qty || 0})</p>
            </div>

            <h2>Today's Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Items</th>
                    </tr>
                </thead>
                <tbody>
                    ${ordersHtml}
                </tbody>
            </table>

            <footer>
                <p>Generated by Crispy King POS System | &copy; ${new Date().getFullYear()} Crispy King</p>
            </footer>

            <script>
                window.onload = function() { window.print(); };
            <\/script>
        </body>
        </html>
    `;

    const printWindow = window.open('', '_blank', 'width=800,height=900');
    if (!printWindow) return;
    printWindow.document.open();
    printWindow.document.write(reportHTML);
    printWindow.document.close();
};
</script>

<template>
    <Head title="Crispy King | Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl bg-gradient-to-br from-orange-50 via-red-50 to-rose-50 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black text-gray-900">🍗 Crispy King Dashboard</h1>
                    <p class="mt-2 text-lg font-medium text-gray-600">Welcome back! Here's your business overview</p>
                </div>
                <button
                    @click="generateReport"
                    class="flex items-center gap-3 rounded-2xl bg-gradient-to-r from-orange-600 to-red-600 px-8 py-4 text-lg font-bold text-white shadow-2xl shadow-orange-500/40 transition-all hover:scale-105 hover:from-orange-700 hover:to-red-700"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>
                    Generate Report
                </button>
            </div>

            <!-- Stats Grid -->
            <div class="grid gap-6 md:grid-cols-3">
                <!-- Total Sales -->
                <div
                    class="group rounded-3xl bg-gradient-to-br from-orange-500 to-red-600 p-8 shadow-xl transition-all hover:-translate-y-2 hover:shadow-2xl"
                >
                    <div class="mb-4 flex items-center justify-between">
                        <div class="rounded-2xl bg-white/20 p-4 backdrop-blur-sm">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                    </div>
                    <p class="mb-2 text-sm font-bold tracking-wider text-white/80 uppercase">Total Sales</p>
                    <h3 class="text-5xl font-black text-white">₱{{ stats.totalSales.toLocaleString() }}</h3>
                </div>

                <!-- Orders Today -->
                <div
                    class="group rounded-3xl bg-gradient-to-br from-amber-500 to-orange-600 p-8 shadow-xl transition-all hover:-translate-y-2 hover:shadow-2xl"
                >
                    <div class="mb-4 flex items-center justify-between">
                        <div class="rounded-2xl bg-white/20 p-4 backdrop-blur-sm">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                />
                            </svg>
                        </div>
                    </div>
                    <p class="mb-2 text-sm font-bold tracking-wider text-white/80 uppercase">Orders Today</p>
                    <h3 class="text-5xl font-black text-white">{{ stats.ordersToday }}</h3>
                </div>

                <!-- Total Customers -->
                <div
                    class="group rounded-3xl bg-gradient-to-br from-red-500 to-rose-600 p-8 shadow-xl transition-all hover:-translate-y-2 hover:shadow-2xl"
                >
                    <div class="mb-4 flex items-center justify-between">
                        <div class="rounded-2xl bg-white/20 p-4 backdrop-blur-sm">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>
                        </div>
                    </div>
                    <p class="mb-2 text-sm font-bold tracking-wider text-white/80 uppercase">Total Customers</p>
                    <h3 class="text-5xl font-black text-white">{{ stats.customers.toLocaleString() }}</h3>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Sales Trend Chart -->
                <div class="rounded-3xl border border-gray-200 bg-white p-8 shadow-xl">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-black text-gray-900">Sales Trend</h2>
                            <p class="mt-1 text-sm font-medium text-gray-500">Last 7 days performance</p>
                        </div>
                        <div class="rounded-xl bg-orange-100 p-3">
                            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                    <Line
                        :data="{
                            labels: salesTrend.labels,
                            datasets: [
                                {
                                    label: 'Sales (₱)',
                                    data: salesTrend.data,
                                    borderColor: '#ea580c',
                                    backgroundColor: 'rgba(234, 88, 12, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                    borderWidth: 3,
                                    pointRadius: 5,
                                    pointHoverRadius: 7,
                                    pointBackgroundColor: '#ea580c',
                                },
                            ],
                        }"
                        :options="{
                            responsive: true,
                            plugins: {
                                legend: { display: false },
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#f3f4f6' },
                                },
                                x: {
                                    grid: { display: false },
                                },
                            },
                        }"
                    />
                </div>

                <!-- Top Products Chart -->
                <div class="rounded-3xl border border-gray-200 bg-white p-8 shadow-xl">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-black text-gray-900">Top Products</h2>
                            <p class="mt-1 text-sm font-medium text-gray-500">Best sellers this period</p>
                        </div>
                        <div class="rounded-xl bg-red-100 p-3">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                />
                            </svg>
                        </div>
                    </div>
                    <Bar
                        :data="{
                            labels: topProducts.map((p) => p.name),
                            datasets: [
                                {
                                    label: 'Units Sold',
                                    data: topProducts.map((p) => p.sold),
                                    backgroundColor: ['#dc2626', '#ea580c', '#f97316', '#fb923c', '#fdba74'],
                                    borderRadius: 8,
                                },
                            ],
                        }"
                        :options="{
                            responsive: true,
                            plugins: {
                                legend: { display: false },
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#f3f4f6' },
                                },
                                x: {
                                    grid: { display: false },
                                },
                            },
                        }"
                    />
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Top Products List -->
                <div class="rounded-3xl border border-gray-200 bg-white p-8 shadow-xl">
                    <h2 class="mb-6 text-2xl font-black text-gray-900">Product Rankings</h2>
                    <div class="space-y-4">
                        <div
                            v-for="(p, index) in topProducts"
                            :key="p.name"
                            class="flex items-center gap-4 rounded-2xl p-4 transition-all hover:bg-gradient-to-r hover:from-orange-50 hover:to-red-50"
                        >
                            <div
                                :class="[
                                    'flex h-12 w-12 items-center justify-center rounded-xl text-lg font-black',
                                    index === 0
                                        ? 'bg-gradient-to-br from-yellow-400 to-yellow-600 text-white'
                                        : index === 1
                                          ? 'bg-gradient-to-br from-gray-300 to-gray-500 text-white'
                                          : index === 2
                                            ? 'bg-gradient-to-br from-orange-400 to-orange-600 text-white'
                                            : 'bg-gradient-to-br from-blue-100 to-blue-200 text-blue-700',
                                ]"
                            >
                                {{ index + 1 }}
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-900">{{ p.name }}</p>
                                <p class="text-sm font-medium text-gray-500">{{ p.sold }} units sold</p>
                            </div>
                            <div class="h-3 w-32 overflow-hidden rounded-full bg-gray-200">
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-purple-500 to-pink-500 transition-all"
                                    :style="{ width: `${(p.sold / topProducts[0].sold) * 100}%` }"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="rounded-3xl border border-gray-200 bg-white p-8 shadow-xl">
                    <h2 class="mb-6 text-2xl font-black text-gray-900">Recent Orders</h2>
                    <div class="space-y-4">
                        <div
                            v-for="o in recentOrders"
                            :key="o.id"
                            class="flex items-center justify-between rounded-2xl border-2 border-gray-100 p-4 transition-all hover:border-blue-300 hover:bg-blue-50/50"
                        >
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-xl font-black text-white"
                                >
                                    {{ o.customer.charAt(0) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">#{{ o.id }}</p>
                                    <p class="text-sm font-medium text-gray-600">{{ o.customer }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-black text-gray-900">{{ o.total }}</p>
                                <p class="text-xs font-medium text-gray-500">{{ o.time }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
