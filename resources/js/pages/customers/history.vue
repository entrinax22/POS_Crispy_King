<template>
    <CustomerLayout>
        <!-- Page Content -->
        <section id="history" class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8 text-center sm:text-left">
                <h2 class="text-2xl font-bold text-orange-700 sm:text-3xl">Purchase History</h2>
                <p class="mt-2 text-sm text-gray-600 sm:text-base">View all your past orders and transactions</p>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="text-center text-gray-600">
                <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-orange-200 border-t-orange-600"></div>
                <p class="mt-2">Loading your orders...</p>
            </div>

            <!-- No Orders -->
            <div v-else-if="orders.length === 0" class="rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center sm:p-12">
                <svg class="mx-auto h-10 w-10 text-gray-400 sm:h-12 sm:w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <p class="mt-4 text-base font-medium text-gray-900 sm:text-lg">No orders yet</p>
                <p class="mt-2 text-sm text-gray-500 sm:text-base">Start shopping to see your purchase history here</p>
            </div>

            <!-- Orders in card format -->
            <div v-else class="space-y-4">
                <div
                    v-for="(order, index) in orders"
                    :key="order.id"
                    @click="viewOrder(order)"
                    class="group cursor-pointer overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:shadow-xl"
                >
                    <!-- Order Header -->
                    <div class="border-b border-gray-100 bg-gradient-to-r from-orange-50 to-white px-4 py-3 sm:px-6 sm:py-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 sm:text-xl">Order #{{ orders.length - index }}</h3>
                                <p class="mt-1 text-xs text-gray-500 sm:text-sm">{{ formatDate(order.created_at) }}</p>
                            </div>
                            <div class="flex flex-wrap items-center justify-between gap-2 sm:justify-end sm:gap-4">
                                <span
                                    class="rounded-full px-3 py-1 text-xs font-semibold shadow-sm sm:text-sm"
                                    :class="{
                                        'bg-yellow-100 text-yellow-800': order.status === 'pending',
                                        'bg-green-100 text-green-800': order.status === 'completed',
                                        'bg-red-100 text-red-800': order.status === 'cancelled',
                                    }"
                                >
                                    {{ order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                                </span>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500 sm:text-sm">Total</p>
                                    <p class="text-lg font-bold text-orange-600 sm:text-2xl">₱{{ formatAmount(order.total_amount) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="divide-y divide-gray-100">
                        <div
                            v-for="item in order.items"
                            :key="item.id"
                            class="flex flex-col gap-3 px-4 py-3 transition-colors hover:bg-gray-50 sm:flex-row sm:items-center sm:px-6 sm:py-4"
                        >
                            <!-- Qty Badge -->
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 font-bold text-orange-600 sm:h-12 sm:w-12"
                            >
                                {{ item.quantity }}x
                            </div>

                            <!-- Item Info -->
                            <div class="min-w-0 flex-1">
                                <h4 class="truncate text-sm font-semibold text-gray-900 sm:text-base">{{ item.product_name }}</h4>
                                <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-600 sm:text-sm">
                                    <span>₱{{ formatAmount(item.price) }} each</span>
                                    <span class="hidden text-gray-400 sm:inline">•</span>
                                    <span class="font-semibold text-orange-600">₱{{ formatAmount(item.total) }} total</span>
                                </div>
                            </div>

                            <!-- Product Image -->
                            <img
                                :src="item.product_image ? `/${item.product_image}` : '/images/placeholder.png'"
                                class="h-12 w-12 rounded border object-cover sm:h-10 sm:w-10"
                                :alt="item.product_name"
                            />
                        </div>
                    </div>

                    <!-- Order Footer -->
                    <div class="bg-gray-50 px-4 py-2 text-center sm:px-6 sm:py-3 sm:text-right">
                        <span class="text-xs font-medium text-gray-600 transition-colors group-hover:text-orange-600 sm:text-sm">
                            Tap to view details →
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal for order details -->
        <BaseModal v-if="selectedOrder" @close="selectedOrder = null">
            <h2 class="mb-2 text-center text-xl font-bold text-orange-700 sm:text-2xl">Order #{{ orders.length - orders.indexOf(selectedOrder) }}</h2>
            <p class="text-center text-xs text-gray-500 sm:text-sm">
                {{ formatDate(selectedOrder.created_at) }}
            </p>

            <div class="mt-4 overflow-x-auto">
                <table class="hidden min-w-full border border-gray-200 text-sm sm:table">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Product</th>
                            <th class="px-4 py-2 text-center">Qty</th>
                            <th class="px-4 py-2 text-right">Price</th>
                            <th class="px-4 py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in selectedOrder.items" :key="item.id" class="border-t">
                            <td class="flex items-center gap-2 px-4 py-2">
                                <img
                                    :src="item.product_image ? `/${item.product_image}` : '/images/placeholder.png'"
                                    class="h-8 w-8 rounded border object-cover"
                                    :alt="item.product_name"
                                />
                                <span>{{ item.product_name }}</span>
                            </td>
                            <td class="px-4 py-2 text-center">{{ item.quantity }}</td>
                            <td class="px-4 py-2 text-right">₱{{ formatAmount(item.price) }}</td>
                            <td class="px-4 py-2 text-right">₱{{ formatAmount(item.total) }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Mobile-friendly stacked list -->
                <div class="space-y-3 sm:hidden">
                    <div v-for="item in selectedOrder.items" :key="item.id" class="flex items-center gap-3 rounded-lg border bg-white p-3 shadow-sm">
                        <img
                            :src="item.product_image ? `/${item.product_image}` : '/images/placeholder.png'"
                            class="h-12 w-12 rounded border object-cover"
                            :alt="item.product_name"
                        />
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ item.product_name }}</p>
                            <p class="text-xs text-gray-500">Qty: {{ item.quantity }}</p>
                            <p class="text-xs text-gray-500">₱{{ formatAmount(item.price) }} each</p>
                        </div>
                        <p class="text-sm font-bold text-orange-600">₱{{ formatAmount(item.total) }}</p>
                    </div>
                </div>
            </div>

            <p class="mt-4 text-right text-base font-semibold sm:text-lg">Total Amount: ₱{{ formatAmount(selectedOrder.total_amount) }}</p>
        </BaseModal>
    </CustomerLayout>
</template>

<script>
import axios from 'axios';
import BaseModal from '../../components/BaseModal.vue';
import CustomerLayout from '../../layouts/CustomerLayout.vue';

export default {
    components: {
        BaseModal,
        CustomerLayout,
    },
    data() {
        return {
            orders: [],
            loading: true,
            selectedOrder: null,
        };
    },
    methods: {
        async fetchOrders() {
            this.loading = true;
            try {
                const response = await axios.get('/orders/user/history');
                this.orders = response.data.data;
            } catch (error) {
                console.error('Error fetching orders:', error);
                this.orders = [];
            } finally {
                this.loading = false;
            }
        },
        formatDate(dateStr) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateStr).toLocaleDateString(undefined, options);
        },
        formatAmount(value) {
            const num = Number(value);
            return isNaN(num) ? '0.00' : num.toFixed(2);
        },
        viewOrder(order) {
            this.selectedOrder = order;
        },
    },
    mounted() {
        this.fetchOrders();
    },
};
</script>
