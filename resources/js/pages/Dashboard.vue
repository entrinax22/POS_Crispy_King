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
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Generate Report Button -->
            <div class="flex justify-end">
                <button @click="generateReport" class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white shadow hover:bg-blue-700">
                    📄 Generate Report
                </button>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-3">
                <div class="flex items-center gap-4 rounded-xl border bg-white p-4 shadow-sm">
                    <span class="text-3xl">💰</span>
                    <div>
                        <div class="text-xl font-bold">₱{{ stats.totalSales.toLocaleString() }}</div>
                        <div class="text-sm text-gray-500">Total Sales</div>
                    </div>
                </div>
                <div class="flex items-center gap-4 rounded-xl border bg-white p-4 shadow-sm">
                    <span class="text-3xl">🛒</span>
                    <div>
                        <div class="text-xl font-bold">{{ stats.ordersToday }}</div>
                        <div class="text-sm text-gray-500">Orders Today</div>
                    </div>
                </div>
                <div class="flex items-center gap-4 rounded-xl border bg-white p-4 shadow-sm">
                    <span class="text-3xl">👥</span>
                    <div>
                        <div class="text-xl font-bold">{{ stats.customers }}</div>
                        <div class="text-sm text-gray-500">Customers</div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-xl border bg-white p-4 shadow-sm">
                    <div class="mb-2 font-semibold">Top Products</div>
                    <ul>
                        <li v-for="p in topProducts" :key="p.name" class="flex justify-between py-1">
                            <span>{{ p.name }}</span>
                            <span class="font-mono">{{ p.sold }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Recent Orders -->
                <div class="rounded-xl border bg-white p-4 shadow-sm">
                    <div class="mb-2 font-semibold">Recent Orders</div>
                    <ul>
                        <li v-for="o in recentOrders" :key="o.id" class="flex justify-between py-1 text-sm">
                            <span>#{{ o.id }} - {{ o.customer }}</span>
                            <span>{{ o.total }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Charts -->
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border bg-white p-4 shadow-sm">
                    <Line
                        :data="{
                            labels: salesTrend.labels,
                            datasets: [
                                {
                                    label: 'Sales (₱)',
                                    data: salesTrend.data,
                                    borderColor: '#22c55e',
                                    backgroundColor: 'rgba(34,197,94,0.1)',
                                    tension: 0.4,
                                    fill: true,
                                },
                            ],
                        }"
                        :options="{
                            responsive: true,
                            plugins: { legend: { display: false }, title: { display: true, text: 'Sales Trend (This Week)' } },
                            scales: { y: { beginAtZero: true } },
                        }"
                    />
                </div>

                <div class="rounded-xl border bg-white p-4 shadow-sm">
                    <Bar
                        :data="{
                            labels: topProducts.map((p) => p.name),
                            datasets: [
                                { label: 'Units Sold', data: topProducts.map((p) => p.sold), backgroundColor: ['#fbbf24', '#f87171', '#60a5fa'] },
                            ],
                        }"
                        :options="{
                            responsive: true,
                            plugins: { legend: { display: false }, title: { display: true, text: 'Top Products' } },
                            scales: { y: { beginAtZero: true } },
                        }"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
