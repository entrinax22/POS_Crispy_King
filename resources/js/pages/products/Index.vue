<template>
    <Head title="Crispy King | Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="text-2xl font-bold">Products</h1>

            <!-- Roles Table -->
            <div class="mr-4 mb-1 flex justify-end">
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
                                placeholder="Search product..."
                            />
                        </div>
                    </form>
                    <button
                        class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium whitespace-nowrap text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        @click="handleOpenModal()"
                    >
                        Add Product
                    </button>
                </div>
            </div>
            <BaseTable
                :columns="columns"
                :rows="products.data ?? []"
                :pagination="products.pagination ?? {}"
                uniqueKey="product_id"
                @page-change="fetchTableData"
            >
                <template #count="{ index }">
                    {{ products.pagination.per_page * (products.pagination.current_page - 1) + index + 1 }}
                </template>

                <template #actions="{ row }">
                    <button
                        class="mr-2 rounded bg-yellow-500 px-3 py-1 font-medium text-white hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-300"
                        @click="fetchProductDetails(row.product_id)"
                    >
                        Edit
                    </button>
                    <button
                        class="rounded bg-red-600 px-3 py-1 font-medium text-white hover:bg-red-700 focus:ring-2 focus:ring-red-300"
                        @click="handleDeleteProduct(row.product_id)"
                    >
                        Delete
                    </button>
                </template>
            </BaseTable>
        </div>

        <!-- Modal Background -->
        <div
            ref="AddModal"
            tabindex="-1"
            class="fixed inset-0 z-50 flex hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-x-hidden overflow-y-auto backdrop-blur-sm md:inset-0"
        >
            <div class="relative mx-auto my-auto flex max-h-full w-full max-w-2xl items-center justify-center p-4">
                <!-- Modal content -->
                <div class="relative rounded-lg bg-white shadow-sm dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between rounded-t border-b border-gray-200 p-4 md:p-5 dark:border-gray-600">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Create Product</h2>
                        <button
                            @click="handleCloseModal"
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
                    <form ref="formRef" @submit.prevent="submitProduct">
                        <div class="grid grid-cols-1 gap-4 space-y-4 p-4 md:grid-cols-2 md:p-5">
                            <div class="mb-4 md:col-span-2">
                                <label for="product-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product Name</label>
                                <input
                                    type="text"
                                    id="product-name"
                                    v-model="newProduct.product_name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>

                            <div class="mb-4 md:col-span-2">
                                <label for="product-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >Product Description</label
                                >
                                <input
                                    type="text"
                                    id="product-description"
                                    v-model="newProduct.product_description"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                            <div class="mb-4">
                                <label for="product-price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product Price</label>
                                <input
                                    type="number"
                                    id="product-price"
                                    v-model="newProduct.product_price"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                            <div class="mb-4">
                                <label for="product-quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >Product Quantity</label
                                >
                                <input
                                    type="number"
                                    id="product-quantity"
                                    v-model="newProduct.product_quantity"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                            <div class="mb-4 md:col-span-2">
                                <label for="product-image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product Image</label>
                                <input
                                    type="file"
                                    id="product-image"
                                    accept="image/*"
                                    @change="onImageChange"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                />
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="flex items-center justify-end rounded-b border-t border-gray-200 p-4 md:p-5 dark:border-gray-600">
                            <button
                                type="submit"
                                class="rounded-lg bg-blue-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            >
                                Create
                            </button>
                            <button
                                type="button"
                                @click="handleCloseModal"
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
            <div class="relative mx-auto my-auto flex max-h-full w-full max-w-2xl items-center justify-center p-4">
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
                    <form ref="formRef" @submit.prevent="submitProduct">
                        <div class="grid grid-cols-1 gap-4 space-y-4 p-4 md:grid-cols-2 md:p-5">
                            <input type="hidden" v-model="newProduct.product_id" />
                            <div class="mb-4 md:col-span-2">
                                <label for="product-name-edit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product Name</label>
                                <input
                                    type="text"
                                    id="product-name-edit"
                                    v-model="newProduct.product_name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                            <div class="mb-4 md:col-span-2">
                                <label for="product-description-edit" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >Product Description</label
                                >
                                <input
                                    type="text"
                                    id="product-description-edit"
                                    v-model="newProduct.product_description"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                            <div class="mb-4">
                                <label for="product-price-edit" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >Product Price</label
                                >
                                <input
                                    type="number"
                                    id="product-price-edit"
                                    v-model="newProduct.product_price"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                            <div class="mb-4">
                                <label for="product-quantity-edit" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >Product Quantity</label
                                >
                                <input
                                    type="number"
                                    id="product-quantity-edit"
                                    v-model="newProduct.product_quantity"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                            <div class="mb-4 md:col-span-2">
                                <label for="product-image-edit" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >Product Image</label
                                >
                                <div class="flex items-center gap-4">
                                    <div
                                        v-if="newProduct.product_image && typeof newProduct.product_image === 'string'"
                                        class="flex h-24 w-24 items-center justify-center overflow-hidden rounded border bg-gray-100 dark:bg-gray-800"
                                    >
                                        <img :src="newProduct.product_image" alt="Product Image" class="max-h-24 max-w-24 object-contain" />
                                    </div>
                                    <input
                                        type="file"
                                        id="product-image-edit"
                                        accept="image/*"
                                        @change="onImageChange"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    />
                                </div>
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
import BaseTable from '@/components/BaseTable.vue';
import { useNotify } from '@/composables/useNotify';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';

const columns = [
    { label: '#', key: 'count' },
    { label: 'Product Name', key: 'product_name' },
    { label: 'Product Code', key: 'product_code' },
    { label: 'Price', key: 'product_price' },
    { label: 'Quantity', key: 'product_quantity' },
    { label: 'Actions', key: 'actions' },
];

const notify = useNotify();

const products = ref<{
    data: Array<{
        product_id: string;
        product_name: string;
        product_description: string;
        product_price: string;
        product_quantity: number;
        product_image: string | null;
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
        const response = await axios.get(route('products.list', { page, search: search.value }));
        if (response.data.result === true) {
            products.value = response.data;
        }
    } catch (error) {
        console.error('Error fetching products:', error);
    }
};

// const fetchRoles = async () => {
//     try {
//         const response = await axios.get(route('roles.list'));
//         if (response.data.result === true) {
//             roles.value = response.data.data;
//         }
//     } catch (error) {
//         console.error('Error fetching permissions:', error);
//     }
// };

const reload = () => {
    fetchTableData(1);
    // fetchRoles();
};

onMounted(() => {
    fetchTableData(1);
    // fetchRoles();
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: route('products.index'),
    },
];

const visible = ref(false);

const newProduct = ref<{
    product_id?: string;
    product_name: string;
    product_description: string;
    product_price: number;
    product_quantity: number;
    product_image: string | File;
}>({
    product_id: '',
    product_name: '',
    product_description: '',
    product_price: 0,
    product_quantity: 0,
    product_image: '',
});

const AddModal = ref<HTMLElement | null>(null);

const handleOpenModal = () => {
    visible.value = true;
    if (AddModal.value) {
        AddModal.value.classList.remove('hidden');
    }
};

const handleCloseModal = () => {
    visible.value = false;
    if (AddModal.value) {
        AddModal.value.classList.add('hidden');
    }
};
const formRef = ref<HTMLFormElement | null>(null);
const submitProduct = async () => {
    const route_url = route('products.store');
    const formData = new FormData();
    if (newProduct.value.product_id && newProduct.value.product_id !== '') {
        formData.append('product_id', newProduct.value.product_id);
    }
    formData.append('product_name', newProduct.value.product_name);
    formData.append('product_description', newProduct.value.product_description);
    formData.append('product_price', newProduct.value.product_price.toString());
    formData.append('product_quantity', newProduct.value.product_quantity.toString());
    // Only append product_image if it's a File (not a string URL)
    if (newProduct.value.product_image && newProduct.value.product_image instanceof File) {
        formData.append('product_image', newProduct.value.product_image);
    }
    try {
        const response = await axios.post(route_url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        if (response.data.result == true) {
            if (formRef.value) formRef.value.reset();
            newProduct.value = {
                product_id: '',
                product_name: '',
                product_description: '',
                product_price: 0,
                product_quantity: 0,
                product_image: '',
            };
            handleCloseModal();
            handleCloseEditModal();
            reload();
            notify(response.data.message, 'success');
        }
    } catch (error: any) {
        notify('Error creating/updating product', 'error');
        console.error('Error:', error.message);
    }
};

const EditModal = ref<HTMLElement | null>(null);

const handleOpenEditModal = () => {
    visible.value = true;
    if (EditModal.value) {
        EditModal.value.classList.remove('hidden');
    }
};

const handleCloseEditModal = () => {
    visible.value = false;
    newProduct.value = {
        product_id: '',
        product_name: '',
        product_description: '',
        product_price: 0,
        product_quantity: 0,
        product_image: '',
    };
    if (EditModal.value) {
        EditModal.value.classList.add('hidden');
    }
};

const fetchProductDetails = async (productId: string) => {
    const route_url = route('products.edit', productId);
    try {
        const response = await axios.get(route_url);
        if (response.data.result === true) {
            // Set role data for editing
            newProduct.value = {
                product_id: response.data.data.product_id,
                product_name: response.data.data.product_name,
                product_description: response.data.data.product_description,
                product_price: response.data.data.product_price,
                product_quantity: response.data.data.product_quantity,
                product_image: response.data.data.product_image || '', // Handle null case
            };
            handleOpenEditModal();
        }
    } catch (error: any) {
        notify('Error fetching role details', 'error');
        console.error('Error:', error.message);
    }
};

const handleDeleteProduct = async (productId: string) => {
    const route_url = route('products.destroy', productId);
    if (confirm('Are you sure you want to delete this product?')) {
        try {
            const response = await axios.post(route_url);
            if (response.status === 200) {
                notify('Product deleted successfully', 'success');
                reload();
            }
        } catch (error) {
            notify('Error deleting product', 'error');
            console.error('Error:', error);
        }
    }
};

// Handle image file input change
const onImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        newProduct.value.product_image = file;
    }
};
</script>
