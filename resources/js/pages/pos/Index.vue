<template>
    <Head title="POS Terminal" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-screen flex-col gap-6 bg-gray-50 p-4 md:flex-row dark:bg-gray-950">
            <!-- Products List -->
            <div
                class="w-full overflow-y-auto rounded-xl border border-gray-200 bg-white p-4 shadow-sm md:w-2/3 dark:border-gray-700 dark:bg-gray-900"
            >
                <h2 class="mb-4 text-xl font-semibold text-gray-800 dark:text-white">Available Products</h2>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 md:grid-cols-3">
                    <div
                        v-for="product in products.data"
                        :key="product.product_id"
                        class="group relative flex cursor-pointer flex-col items-center rounded-xl bg-gray-100 p-4 shadow-sm transition hover:bg-yellow-50 hover:shadow-md dark:bg-gray-800 dark:hover:bg-gray-700"
                    >
                        <img :src="product.product_image" :alt="product.product_name" class="mb-2 h-24 w-24 rounded border object-cover" />
                        <div class="text-center">
                            <div class="font-medium text-gray-700 group-hover:text-yellow-600 dark:text-gray-200">{{ product.product_name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ product.product_code }}</div>
                            <div class="mt-1 text-lg font-bold text-yellow-600">₱{{ product.product_price }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- POS Terminal (Cart) -->
            <div class="flex w-full flex-col rounded-xl border border-gray-200 bg-white p-4 shadow-sm md:w-1/3 dark:border-gray-700 dark:bg-gray-900">
                <div class="mb-4 text-xl font-semibold text-gray-800 dark:text-white">Order Cart</div>

                <!-- Order Type Options -->
                <div class="mb-4 flex items-center gap-3">
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                        <input type="radio" name="orderType" value="dine-in" checked class="accent-yellow-500" />
                        Dine In
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                        <input type="radio" name="orderType" value="take-out" class="accent-yellow-500" />
                        Take Out
                    </label>
                    <button class="ml-auto text-sm text-yellow-700 hover:underline dark:text-yellow-400">Edit</button>
                </div>

                <!-- Cart List -->
                <div v-if="cart.length" class="mb-4 max-h-72 flex-1 overflow-y-auto">
                    <ul>
                        <li v-for="item in cart" :key="item.id" class="flex items-center justify-between border-b py-2 dark:border-gray-800">
                            <div>
                                <div class="font-semibold text-gray-800 dark:text-white">{{ item.name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Qty: {{ item.qty }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-gray-700 dark:text-gray-200">₱{{ item.price * item.qty }}</div>
                                <button class="text-xs text-red-500 hover:underline">Remove</button>
                            </div>
                        </li>
                    </ul>
                </div>
                <div v-else class="flex flex-1 items-center justify-center text-gray-400">Cart is empty</div>

                <!-- Checkout Summary -->
                <div class="mt-auto border-t pt-4 dark:border-gray-700">
                    <div class="flex justify-between text-lg font-semibold text-gray-800 dark:text-white">
                        <span>Total</span>
                        <span>₱{{ cartTotal }}</span>
                    </div>
                    <button class="mt-4 w-full rounded-lg bg-yellow-500 py-2 font-bold text-white shadow transition hover:bg-yellow-600">
                        Proceed to Checkout
                    </button>
                </div>
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
import { onMounted, ref } from 'vue';

const notify = useNotify();

const products = ref<
    Array<{
        product_id: string;
        product_name: string;
        product_code: string;
        product_description: string;
        product_price: number;
        product_quantity: number;
        product_image: string | File;
    }>
>([]);

const search = ref<string>('');
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'POS Terminal',
        href: '/pos',
    },
];

const fetchProductData = async (page = 1) => {
    try {
        const response = await axios.get(route('products.list', { page, search: search.value }));
        if (response.data.result === true) {
            products.value = response.data;
        }
    } catch (error) {
        console.error('Error fetching products:', error);
    }
};

const reload = () => {
    fetchProductData();
};

onMounted(() => {
    fetchProductData(1);
    // fetchRoles();
});

// // Dummy product data
// const products = [
//     { id: 1, name: 'Crispy Chicken', price: 120, image: 'https://via.placeholder.com/80' },
//     { id: 2, name: 'Spicy Wings', price: 95, image: 'https://via.placeholder.com/80' },
//     { id: 3, name: 'Fries', price: 80, image: 'https://via.placeholder.com/80' },
//     { id: 5, name: 'Soda', price: 40, image: 'https://via.placeholder.com/80' },
// ];

// Dummy cart data
const cart = [
    { id: 1, name: 'Crispy Chicken', qty: 2, price: 120 },
    { id: 3, name: 'Fries', qty: 1, price: 80 },
];

const cartTotal = cart.reduce((sum, item) => sum + item.qty * item.price, 0);
</script>
