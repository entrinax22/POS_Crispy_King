<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'POS Terminal',
        href: '/pos',
    },
];

// Dummy product data
const products = [
    { id: 1, name: 'Crispy Chicken', price: 120, image: 'https://via.placeholder.com/80' },
    { id: 2, name: 'Spicy Wings', price: 95, image: 'https://via.placeholder.com/80' },
    { id: 3, name: 'Fries', price: 80, image: 'https://via.placeholder.com/80' },
    { id: 5, name: 'Soda', price: 40, image: 'https://via.placeholder.com/80' },
];

// Dummy cart data
const cart = [
    { id: 1, name: 'Crispy Chicken', qty: 2, price: 120 },
    { id: 3, name: 'Fries', qty: 1, price: 80 },
];

const cartTotal = cart.reduce((sum, item) => sum + item.qty * item.price, 0);
</script>

<template>
    <Head title="POS Terminal" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col md:flex-row gap-6 p-4 h-[80vh]">
            
            <!-- Products List -->
            <div class="w-full md:w-2/3 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 overflow-y-auto">
                <div class="font-bold text-lg mb-4 text-gray-700 dark:text-gray-200">Products</div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div v-for="product in products" :key="product.id" class="flex flex-col items-center bg-gray-50 dark:bg-gray-800 rounded-lg p-3 shadow hover:shadow-lg transition cursor-pointer">
                        <img :src="product.image" :alt="product.name" class="w-20 h-20 object-cover rounded mb-2" />
                        <div class="font-semibold text-gray-700 dark:text-gray-200">{{ product.name }}</div>
                        <div class="text-primary font-bold mb-2">₱{{ product.price }}</div>
                    </div>
                </div>
            </div>

            <!-- POS Terminal (Cart) -->
            <div class="w-full md:w-1/3 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex flex-col">
                <div class="font-bold text-lg mb-4 text-gray-700 dark:text-gray-200">Cart</div>
                <!-- Order Options -->
                <div class="flex items-center gap-2 mb-4">
                    <label class="flex items-center gap-1 cursor-pointer">
                        <input type="radio" name="orderType" value="dine-in" checked class="accent-yellow-500" />
                        <span class="text-sm text-gray-700 dark:text-gray-200">Dine In</span>
                    </label>
                    <label class="flex items-center gap-1 cursor-pointer">
                        <input type="radio" name="orderType" value="take-out" class="accent-yellow-500" />
                        <span class="text-sm text-gray-700 dark:text-gray-200">Take Out</span>
                    </label>
                    <button class="ml-auto text-xs text-yellow-700 dark:text-yellow-300 hover:underline px-2 py-1 rounded transition">Edit</button>
                </div>
                <div v-if="cart.length" class="flex-1 overflow-y-auto mb-4">
                    <ul>
                        <li v-for="item in cart" :key="item.id" class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                            <div>
                                <div class="font-semibold">{{ item.name }}</div>
                                <div class="text-xs text-gray-500">Qty: {{ item.qty }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold">₱{{ item.price * item.qty }}</div>
                                <button class="text-xs text-red-500 hover:underline mt-1">Remove</button>
                            </div>
                        </li>
                    </ul>
                </div>
                <div v-else class="text-gray-400 text-center flex-1 flex items-center justify-center">Cart is empty</div>
                <div class="mt-4 border-t pt-4 border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between font-semibold text-lg">
                        <span>Total</span>
                        <span>₱{{ cartTotal }}</span>
                    </div>
                    <button class="w-full mt-4 bg-primary text-white py-2 rounded-lg font-bold hover:bg-primary-dark transition">Checkout</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
