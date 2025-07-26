<template>
    <Head title="Crispy King | Orders" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="text-2xl font-bold">Orders</h1>

            <!-- Tables Table -->
            <div class="mr-0 mb-1 flex justify-end">
                <div class="flex w-full max-w-lg items-center gap-4">
                    <form class="flex-1" @submit.prevent>
                        <label for="default-search" class="sr-only mb-2 text-sm font-medium text-gray-900 dark:text-white">Search</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
                                <svg
                                    class="h-4 w-4 text-gray-500 dark:text-gray-400"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                                    />
                                </svg>
                            </div>
                            <input
                                type="search"
                                id="default-search"
                                v-model="search"
                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2 ps-10 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                placeholder="Search permissions..."
                            />
                        </div>
                    </form>
                    <!-- <button
                        class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium whitespace-nowrap text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        @click="handleOpenModal()"
                    >
                        Add Order
                    </button> -->
                </div>
            </div>
            <BaseTable
                :columns="columns"
                :rows="orders.data ?? []"
                :pagination="orders.pagination ?? {}"
                uniqueKey="table_id"
                @page-change="fetchTableData"
            >
                <template #count="{ index }">
                    {{ orders.pagination.per_page * (orders.pagination.current_page - 1) + index + 1 }}
                </template>

                <template #ordered_items="{ row }">
                    <ul v-if="row.ordered_items && row.ordered_items.length" class="list-none ps-5">
                        <li v-for="item in row.ordered_items" :key="item.product_id">
                            {{ item.product_name }} ({{ item.quantity }}) - ₱{{ parseFloat(item.total).toFixed(2) }}
                        </li>
                    </ul>
                    <div v-else class="text-gray-500 italic">No items</div>
                </template>

                <template #actions="{ row }">
                    <button
                        class="mr-2 rounded bg-yellow-500 px-3 py-1 font-medium text-white hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-300"
                        @click="fetchTableDetails(row.order_id)"
                    >
                        Edit
                    </button>
                    <button
                        class="rounded bg-red-600 px-3 py-1 font-medium text-white hover:bg-red-700 focus:ring-2 focus:ring-red-300"
                        @click="handleDeleteOrder(row.order_id)"
                    >
                        Delete
                    </button>
                </template>
            </BaseTable>
        </div>

        <!-- Edit Order Modal -->
        <div
            ref="EditModal"
            tabindex="-1"
            class="fixed inset-0 z-50 flex hidden h-full w-full items-center justify-center overflow-auto bg-black/30 backdrop-blur-sm"
        >
            <div class="relative mx-auto my-8 w-full max-w-4xl px-4">
                <div class="rounded-xl bg-white shadow-lg dark:bg-gray-800">
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b px-6 py-4 dark:border-gray-700">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Order</h2>
                        <button @click="handleCloseEditModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitEditOrder">
                        <!-- Order Info -->
                        <div class="grid grid-cols-1 gap-6 px-6 py-4 md:grid-cols-2">
                            <input type="hidden" v-model="editOrder.order_id" />

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order Type</label>
                                <select v-model="editOrder.order_type" class="form-select w-full dark:bg-gray-700 dark:text-white">
                                    <option value="dine_in">Dine In</option>
                                    <option value="take_out">Take Out</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select v-model="editOrder.status" class="form-select w-full dark:bg-gray-700 dark:text-white">
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <!-- Product Selector -->
                        <div class="border-t border-b px-6 py-4 dark:border-gray-700">
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Add Product</label>
                            <div class="flex gap-2">
                                <select v-model="selectedProduct" class="form-select flex-1 dark:bg-gray-700 dark:text-white">
                                    <option disabled value="">Select a product</option>
                                    <option v-for="product in editOrder.products" :key="product.product_id" :value="product">
                                        {{ product.product_name }} - ₱{{ product.price }}
                                    </option>
                                </select>
                                <button
                                    @click="addProductToOrder"
                                    type="button"
                                    class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                                >
                                    Add
                                </button>
                            </div>
                        </div>

                        <!-- Ordered Items -->
                        <div class="px-6 py-4">
                            <h3 class="mb-3 text-lg font-semibold text-gray-800 dark:text-gray-200">Ordered Items</h3>

                            <div
                                v-for="(item, index) in editOrder.ordered_items"
                                :key="item.product_id"
                                class="mb-4 rounded-lg border p-4 shadow-sm dark:border-gray-600 dark:bg-gray-700"
                            >
                                <div class="grid grid-cols-1 items-center gap-4 md:grid-cols-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600 dark:text-gray-300">Product</label>
                                        <input
                                            type="text"
                                            :value="item.product_name"
                                            disabled
                                            class="w-full rounded-md border-gray-300 dark:bg-gray-600 dark:text-white"
                                        />
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-gray-600 dark:text-gray-300">Quantity</label>
                                        <input
                                            type="number"
                                            min="1"
                                            v-model.number="item.quantity"
                                            @input="updateItemTotal(index)"
                                            class="w-full rounded-md border-gray-300 dark:bg-gray-600 dark:text-white"
                                        />
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-gray-600 dark:text-gray-300">Total</label>
                                        <input
                                            type="text"
                                            :value="'₱' + (item.quantity * item.price).toFixed(2)"
                                            disabled
                                            class="w-full rounded-md border-gray-300 dark:bg-gray-600 dark:text-white"
                                        />
                                    </div>

                                    <div class="mt-6 flex items-center justify-end md:mt-0">
                                        <button type="button" @click="removeItem(index)" class="text-sm text-red-600 hover:underline">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-between border-t px-6 py-4 dark:border-gray-700">
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">Total Amount: ₱{{ totalAmount }}</p>
                            <div class="flex gap-2">
                                <button
                                    type="submit"
                                    class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                >
                                    Save Changes
                                </button>
                                <button
                                    type="button"
                                    @click="handleCloseEditModal"
                                    class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import BaseTable from '@/components/BaseTable.vue';
import { useNotify } from '@/composables/useNotify';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref, watch } from 'vue';

const columns = [
    { label: '#', key: 'count' },
    { label: 'Customer Name', key: 'customer_name' },
    { label: 'Order Type', key: 'order_type' },
    { label: 'Total Amount', key: 'total_amount' },
    { label: 'Status', key: 'status' },
    { label: 'Ordered Items', key: 'ordered_items' },
    // { label: 'Total Amount', key: 'total_amount' },
    { label: 'Actions', key: 'actions' },
];

const notify = useNotify();

const orders = ref<{
    data: Array<{
        order_id: string;
        customer_name: string;
        status: string;
        total_amount: number;
        order_type: string;
        ordered_items: Array<{
            product_id: string;
            product_name: string;
            quantity: number;
            price: number;
            total: number;
        }>;
        products: Array<{
            product_id: string;
            product_name: string;
            price: number;
        }>;
    }>;
    pagination: {
        total: number;
        current_page: number;
        last_page: number;
        per_page: number;
    };
}>({
    data: [],
    pagination: {
        total: 0,
        current_page: 1,
        last_page: 1,
        per_page: 10,
    },
});

const search = ref<string>('');
let searchTimeout: ReturnType<typeof setTimeout> | null = null;

watch(search, () => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchTableData(1);
    }, 1000);
});

const fetchTableData = async (page = 1) => {
    try {
        const response = await axios.get(route('orders.list', { page, search: search.value }));
        if (response.data.result === true) {
            orders.value = response.data;
        }
    } catch (error) {
        console.error('Error fetching products:', error);
    }
};

const reload = () => {
    fetchTableData(1);
};

onMounted(() => {
    fetchTableData(1);
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Orders',
        href: route('orders.index'),
    },
];

const visible = ref(false);

// Table Edit Modal logic
const EditModal = ref<HTMLElement | null>(null);
const editOrder = ref<{
    order_id: string;
    customer_name: string;
    order_type: string;
    total_amount: number;
    status: string;
    ordered_items: Array<{
        product_id: string;
        product_name: string;
        quantity: number;
        price: number;
        total: number;
    }>;
    products: Array<{
        product_id: string;
        product_name: string;
        price: number;
    }>;
}>({
    order_id: '',
    customer_name: '',
    order_type: '',
    total_amount: 0,
    status: '',
    ordered_items: [],
    products: [],
});

const handleOpenEditModal = () => {
    visible.value = true;
    if (EditModal.value) {
        EditModal.value.classList.remove('hidden');
    }
};

const handleCloseEditModal = () => {
    visible.value = false;
    editOrder.value = {
        order_id: '',
        customer_name: '',
        order_type: '',
        total_amount: 0,
        status: '',
        ordered_items: [],
        products: [],
    };
    if (EditModal.value) {
        EditModal.value.classList.add('hidden');
    }
};
const selectedProduct = ref('');

const addProductToOrder = () => {
    if (!selectedProduct.value) return;

    const exists = editOrder.value.ordered_items.find((item) => item.product_id === selectedProduct.value.product_id);

    if (exists) {
        exists.quantity += 1;
    } else {
        editOrder.value.ordered_items.push({
            product_id: selectedProduct.value.product_id,
            product_name: selectedProduct.value.product_name,
            quantity: 1,
            price: parseFloat(selectedProduct.value.price),
            total: parseFloat(selectedProduct.value.price),
        });
    }

    selectedProduct.value = '';
    calculateTotalAmount();
};

const updateItemTotal = (index) => {
    const item = editOrder.value.ordered_items[index];
    item.total = item.quantity * item.price;
    calculateTotalAmount();
};

const removeItem = (index) => {
    editOrder.value.ordered_items.splice(index, 1);
    calculateTotalAmount();
};

const calculateTotalAmount = () => {
    editOrder.value.total_amount = editOrder.value.ordered_items.reduce((sum, item) => sum + item.quantity * item.price, 0);
};

const totalAmount = computed(() => editOrder.value.total_amount);

const fetchTableDetails = async (orderId: string) => {
    const route_url = route('orders.edit', orderId);
    try {
        const response = await axios.get(route_url);

        if (response.data.result === true) {
            const data = response.data.data;

            editOrder.value = {
                order_id: data.order_id,
                customer_name: '',
                order_type: data.order_type,
                total_amount: data.total_amount,
                status: data.status,
                ordered_items: data.ordered_items.map((item: any) => ({
                    product_id: item.product_id,
                    product_name: item.product_name,
                    quantity: item.quantity,
                    price: item.price,
                    total: item.total,
                })),
                products: data.products.map((product: any) => ({
                    product_id: product.product_id,
                    product_name: product.product_name,
                    price: parseFloat(product.product_price).toFixed(2),
                })),
            };

            handleOpenEditModal();
        }
    } catch (error: any) {
        notify('Error fetching order details', 'error');
    }
};

const submitEditOrder = async () => {
    const route_url = route('orders.store');
    const payload = {
        order_id: editOrder.value.order_id,
        order_type: editOrder.value.order_type,
        status: editOrder.value.status,
        ordered_items: editOrder.value.ordered_items.map((item) => ({
            product_id: item.product_id,
            quantity: item.quantity,
            price: item.price,
            total: item.total,
        })),
        total_amount: editOrder.value.total_amount,
    };
    try {
        const response = await axios.post(route_url, payload);
        if (response.data.result === true) {
            handleCloseEditModal();
            reload();
            notify(response.data.message, 'success');
        }
    } catch (error: any) {
        notify('Error updating order', 'error');
    }
};

const handleDeleteOrder = async (orderId: string) => {
    const route_url = route('orders.destroy', orderId);
    if (confirm('Are you sure you want to delete this order?')) {
        try {
            const response = await axios.post(route_url);
            if (response.status === 200) {
                notify('Table deleted successfully', 'success');
                reload();
            }
        } catch (error) {
            notify('Error deleting order', 'error');
        }
    }
};
</script>
