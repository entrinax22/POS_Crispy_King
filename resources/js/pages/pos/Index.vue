<template>
    <Head title="Crispy King | POS" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-screen flex-col gap-6 bg-gray-50 p-4 md:flex-row dark:bg-gray-950">
            <!-- Products List -->
            <div
                class="w-full overflow-y-auto rounded-xl border border-gray-200 bg-white p-4 shadow-sm md:w-2/3 dark:border-gray-700 dark:bg-gray-900"
            >
                <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="mb-0 text-xl font-semibold text-gray-800 dark:text-white">Available Products</h2>
                    <form class="w-full sm:w-auto" @submit.prevent>
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
                                placeholder="Search product..."
                            />
                        </div>
                    </form>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 md:grid-cols-3">
                    <div
                        v-for="product in products.data"
                        :key="product.product_id"
                        class="group relative flex cursor-pointer flex-col items-center rounded-xl bg-gray-100 p-4 shadow-sm transition hover:bg-yellow-50 hover:shadow-md dark:bg-gray-800 dark:hover:bg-gray-700"
                        @click="addToCart(product)"
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
            <div
                class="flex h-[80vh] max-h-[900px] min-h-[500px] w-full flex-col rounded-xl border border-gray-200 bg-white p-4 shadow-sm md:w-1/3 dark:border-gray-700 dark:bg-gray-900"
            >
                <div class="mb-4 text-xl font-semibold text-gray-800 dark:text-white">Order Cart</div>

                <!-- Order Type Options -->
                <div class="mb-4 flex items-center gap-3">
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                        <input
                            type="radio"
                            name="orderType"
                            value="dine_in"
                            :checked="orderType === 'dine_in'"
                            class="accent-yellow-500"
                            @change="setOrderType('dine_in')"
                        />
                        Dine In
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                        <input
                            type="radio"
                            name="orderType"
                            value="take_out"
                            :checked="orderType === 'take_out'"
                            class="accent-yellow-500"
                            @change="setOrderType('take_out')"
                        />
                        Take Out
                    </label>
                </div>

                <!-- Cart List -->
                <div v-if="cart.length" class="mb-4 min-h-[80px] flex-1 overflow-y-auto">
                    <ul>
                        <li v-for="item in cart" :key="item.product_id" class="flex items-center justify-between border-b py-2 dark:border-gray-800">
                            <div>
                                <div class="font-semibold text-gray-800 dark:text-white">{{ item.product_name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Qty: {{ item.product_quantity }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-gray-700 dark:text-gray-200">₱{{ item.product_price * item.product_quantity }}</div>
                                <button class="text-l px-2 text-orange-500 hover:underline" @click="() => handleOpenEditModal(item)">Edit</button>
                                <button class="text-l text-red-500 hover:underline" @click="removeFromCart(item.product_id)">Remove</button>
                            </div>
                        </li>
                    </ul>
                </div>
                <div v-else class="flex flex-1 items-center justify-center text-gray-400">Cart is empty</div>

                <!-- Checkout Summary -->
                <div class="sticky right-0 bottom-0 left-0 z-10 border-t bg-white pt-4 dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex justify-between text-lg font-semibold text-gray-800 dark:text-white">
                        <span>Total</span>
                        <span>₱{{ cartTotal }}</span>
                    </div>
                    <button
                        class="mt-4 w-full rounded-lg bg-yellow-500 py-2 font-bold text-white shadow transition hover:bg-yellow-600"
                        @click="proceedToCheckout"
                    >
                        Proceed to Checkout
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Product Modal -->
        <div
            ref="AddModal"
            tabindex="-1"
            class="fixed inset-0 z-50 flex hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-x-hidden overflow-y-auto backdrop-blur-sm md:inset-0"
        >
            <div class="relative flex max-h-full w-full max-w-2xl items-center justify-center p-4">
                <!-- Modal content -->
                <div class="relative w-full rounded-lg bg-white shadow-sm dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between rounded-t border-b border-gray-200 p-4 md:p-5 dark:border-gray-600">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Add Product</h2>
                        <button
                            @click="handleCloseAddModal"
                            type="button"
                            class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white"
                        >
                            <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                                />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <form ref="formRef" @submit.prevent="handleAddProduct">
                        <div class="gap-4 space-y-4 p-4 md:grid-cols-2 md:p-5">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product Name</label>
                                <input
                                    type="text"
                                    v-model="newProduct.product_name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                    readonly
                                />
                            </div>
                            <div class="mb-4">
                                <label for="product-qty-edit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                <input
                                    type="number"
                                    id="product-qty-edit"
                                    v-model="newProduct.quantity"
                                    min="1"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="flex items-center justify-end rounded-b border-t border-gray-200 p-4 md:p-5 dark:border-gray-600">
                            <button
                                type="submit"
                                class="rounded-lg bg-blue-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            >
                                Save Changes
                            </button>
                            <button
                                type="button"
                                @click="handleCloseAddModal"
                                class="ms-3 rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div
            ref="EditModal"
            tabindex="-1"
            class="fixed inset-0 z-50 flex hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-x-hidden overflow-y-auto backdrop-blur-sm md:inset-0"
        >
            <div class="relative flex max-h-full w-full max-w-2xl items-center justify-center p-4">
                <!-- Modal content -->
                <div class="relative w-full rounded-lg bg-white shadow-sm dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between rounded-t border-b border-gray-200 p-4 md:p-5 dark:border-gray-600">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Edit Product</h2>
                        <button
                            @click="handleCloseEditModal"
                            type="button"
                            class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white"
                        >
                            <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                                />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <form ref="formRef" @submit.prevent="handleEditProduct">
                        <div class="gap-4 space-y-4 p-4 md:grid-cols-2 md:p-5">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product Name</label>
                                <input
                                    type="text"
                                    :value="editCartItem?.product_name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                    readonly
                                />
                            </div>
                            <div class="mb-4">
                                <label for="edit-qty" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                <input
                                    type="number"
                                    id="edit-qty"
                                    v-model="editQuantity"
                                    min="1"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="flex items-center justify-end rounded-b border-t border-gray-200 p-4 md:p-5 dark:border-gray-600">
                            <button
                                type="submit"
                                class="rounded-lg bg-blue-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            >
                                Save Changes
                            </button>
                            <button
                                type="button"
                                @click="handleCloseEditModal"
                                class="ms-3 rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
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
import { computed, onMounted, ref } from 'vue';

const notify = useNotify();
const products = ref<any>({ data: [] });
const search = ref<string>('');
const cart = ref<Array<{ product_id: string; product_name: string; product_quantity: number; product_price: number }>>([]);
const orderType = ref<string>('dine_in');
const cartTotal = computed(() => cart.value.reduce((sum, item) => sum + item.product_quantity * item.product_price, 0));
const AddModal = ref<HTMLElement | null>(null);
const addModalVisible = ref<boolean>(false);
const newProduct = ref({
    product_id: '',
    product_name: '',
    product_price: 0,
    quantity: 1,
});

const handleOpenAddModal = (product: any) => {
    addModalVisible.value = true;
    if (AddModal.value) {
        AddModal.value.classList.remove('hidden');
    }
    newProduct.value.product_id = product.product_id;
    newProduct.value.product_name = product.product_name;
    newProduct.value.product_price = product.product_price;
    newProduct.value.quantity = 1;
};

const handleAddProduct = () => {
    if (!newProduct.value.product_id || !newProduct.value.quantity || newProduct.value.quantity < 1) return;
    const existingItem = cart.value.find((item) => item.product_id === newProduct.value.product_id);
    if (existingItem) {
        existingItem.product_quantity += Number(newProduct.value.quantity);
    } else {
        cart.value.push({
            product_id: newProduct.value.product_id,
            product_name: newProduct.value.product_name,
            product_quantity: Number(newProduct.value.quantity),
            product_price: Number(newProduct.value.product_price),
        });
    }
    handleCloseAddModal();
    notify('Product Added Successfully', 'success');
};

const handleCloseAddModal = () => {
    addModalVisible.value = false;
    if (AddModal.value) {
        AddModal.value.classList.add('hidden');
    }
    newProduct.value.product_id = '';
    newProduct.value.product_name = '';
    newProduct.value.product_price = 0;
    newProduct.value.quantity = 1;
};

const addToCart = (product: any) => {
    handleOpenAddModal(product);
};

const EditModal = ref<HTMLElement | null>(null);

// Edit Modal State
const editModalVisible = ref(false);
const editCartItem = ref<{ product_id: string; product_name: string; product_quantity: number; product_price: number } | null>(null);
const editQuantity = ref(1);

const handleOpenEditModal = (item: { product_id: string; product_name: string; product_quantity: number; product_price: number }) => {
    editCartItem.value = { ...item };
    editQuantity.value = item.product_quantity;
    editModalVisible.value = true;
    if (EditModal.value) {
        EditModal.value.classList.remove('hidden');
    }
};

const handleCloseEditModal = () => {
    editModalVisible.value = false;
    if (EditModal.value) {
        EditModal.value.classList.add('hidden');
    }
    editCartItem.value = null;
    editQuantity.value = 1;
};

const handleEditProduct = () => {
    if (!editCartItem.value || !editQuantity.value || editQuantity.value < 1) return;
    const found = cart.value.find((item) => item.product_id === editCartItem.value!.product_id);
    if (found) {
        found.product_quantity = Number(editQuantity.value);
    }
    handleCloseEditModal();
    notify(`Product quantity updated!`, 'success');
};

const removeFromCart = (id: string) => {
    cart.value = cart.value.filter((item) => item.product_id !== id);
};

const setOrderType = (type: string) => {
    orderType.value = type;
};

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

const proceedToCheckout = async () => {
    if (!cart.value.length) {
        notify.error('Cart is empty!');
        return;
    }
    try {
        const response = await axios.post(route('pos.checkout'), {
            cart: cart.value,
            total_amount: cartTotal.value,
            orderType: orderType.value,
        });
        if (response.data.result) {
            notify('Order placed successfully!', 'success');
            cart.value = [];
        } else {
            notify(response.data.message || 'Checkout failed.', 'error');
        }
    } catch (error: any) {
        notify(error?.response?.data?.message || error.message || 'Checkout failed.', 'error');
    }
};
</script>
