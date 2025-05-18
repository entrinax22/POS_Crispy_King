<template>

    <Head title="Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="text-2xl font-bold">Products</h1>

            <!-- Roles Table -->
            <div class="mr-4 mb-1 flex justify-end">

                <div class="flex items-center gap-4 w-full max-w-lg">
                    <form class="flex-1" @submit.prevent>
                        <label for="default-search"
                            class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input type="search" id="default-search" v-model="search"
                                class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search permissions..." />
                        </div>
                    </form>
                    <button
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 whitespace-nowrap"
                        @click="handleOpenModal()">Add Product
                    </button>
                </div>
            </div>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Product Name</th>
                            <th scope="col" class="px-6 py-3">Product Code</th>
                            <th scope="col" class="px-6 py-3">Price</th>
                            <th scope="col" class="px-6 py-3">Quantity</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in products.data" :key="product.product_id"
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{
                                product.product_name }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{
                                product.product_code }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{
                                product.product_price }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{
                                product.product_quantity }}
                            </td>
                            
                            <td class="px-6 py-4">
                                <button
                                    class="text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-300 font-medium rounded px-3 py-1 mr-2"
                                    @click="fetchUserDetails(user.id)">
                                    Edit
                                </button>
                                <button
                                    class="text-white bg-red-600 hover:bg-red-700 focus:ring-2 focus:ring-red-300 font-medium rounded px-3 py-1"
                                    @click="handleDeleteUser(user.id)">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot v-if="users.pagination">
                        <tr>
                            <td colspan="1" class="px-6 py-4">
                                <span class="text-sm text-gray-600">
                                    Total: {{ users.pagination.total }} users
                                </span>
                            </td>
                            <td colspan="3" class="px-6 py-4">
                                <div class="flex justify-end items-center gap-2">
                                    <button :disabled="users.pagination.current_page === 1"
                                        @click="fetchTableData(users.pagination.current_page - 1)"
                                        class="px-2 py-1 rounded border bg-gray-200 hover:bg-gray-300 disabled:opacity-50"
                                        aria-label="Previous page">
                                        &laquo;
                                    </button>
                                    <template v-for="page in Math.min(users.pagination.last_page, 5)" :key="page">
                                        <button
                                            v-if="page === 1 || page === users.pagination.last_page || Math.abs(page - users.pagination.current_page) <= 1"
                                            :class="[
                                                'px-3 py-1 rounded border',
                                                page === users.pagination.current_page
                                                    ? 'bg-blue-600 text-white'
                                                    : 'bg-gray-200 hover:bg-gray-300'
                                            ]" @click="fetchTableData(page)"
                                            :disabled="page === users.pagination.current_page">
                                            {{ page }}
                                        </button>
                                        <span
                                            v-else-if="page === users.pagination.current_page - 2 || page === users.pagination.current_page + 2"
                                            class="px-2">...</span>
                                    </template>
                                    <button :disabled="users.pagination.current_page === users.pagination.last_page"
                                        @click="fetchTableData(users.pagination.current_page + 1)"
                                        class="px-2 py-1 rounded border bg-gray-200 hover:bg-gray-300 disabled:opacity-50"
                                        aria-label="Next page">
                                        &raquo;
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Modal Background -->
        <div ref="AddModal" tabindex="-1" aria-hidden="true"
            class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm overflow-y-auto overflow-x-hidden w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full flex items-center justify-center mx-auto my-auto">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                    <!-- Modal header -->
                    <div
                        class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Create Product</h2>
                        <button @click="handleCloseModal" type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <form ref="formRef" @submit.prevent="submitProduct">
                        <div class="p-4 md:p-5 space-y-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="product-name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product
                                    Name</label>
                                <input type="text" id="product-name" v-model="newProduct.product_name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required />
                            </div>
                            <div class="mb-4">
                                <label for="product-code"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product
                                    Code</label>
                                <input type="text" id="product-code" v-model="newProduct.product_code"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required />
                            </div>
                            <div class="mb-4 md:col-span-2">
                                <label for="product-description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product
                                    Description</label>
                                <input type="text" id="product-description" v-model="newProduct.product_description"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required />
                            </div>
                            <div class="mb-4">
                                <label for="product-price"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product
                                    Price</label>
                                <input type="number" id="product-price" v-model="newProduct.product_price"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required />
                            </div>
                            <div class="mb-4">
                                <label for="product-quantity"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product
                                    Quantity</label>
                                <input type="number" id="product-quantity" v-model="newProduct.product_quantity"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required />
                            </div>
                            <div class="mb-4 md:col-span-2">
                                <label for="product-image"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product
                                    Image</label>
                                <input type="file" id="product-image" accept="image/*" @change="onImageChange"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white" />
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <button type="submit"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Create</button>
                            <button type="button" @click="handleCloseModal"
                                class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <!-- <div ref="EditModal" tabindex="-1" aria-hidden="true"
            class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm overflow-y-auto overflow-x-hidden w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full flex items-center justify-center mx-auto my-auto"> -->
        <!-- Modal content -->
        <!-- <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700"> -->
        <!-- Modal header -->
        <!-- <div
                        class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Edit User</h2>
                        <button @click="handleCloseEditModal" type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div> -->
        <!-- Modal body -->
        <!-- <form ref="formRef" @submit.prevent="submitUser">
                        <div class="p-4 md:p-5 space-y-4">
                            <input type="hidden" id="role-id" v-model="newUser.id" />
                            <div class="mb-4">
                                <label for="role-name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">User
                                    Name</label>
                                <input type="text" id="role-name" v-model="newUser.name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required />
                            </div>
                            <div class="mb-4">
                                <label for="role-name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">User
                                    Email</label>
                                <input type="text" id="role-name" v-model="newUser.email"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required />
                            </div>
                            <div class="mb-4">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Roles</label>
                                <div class="grid grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                                    <div v-for="role in roles" :key="role.id" class="flex items-center m-3">
                                        <input type="checkbox" :id="'perm-' + role.id" :value="role.id"
                                            v-model="newUser.roles"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white" />
                                        <label :for="'perm-' + role.id" class="ml-2 text-gray-700 dark:text-gray-300">{{
                                            role.name }}</label>
                                    </div>
                                </div>
                            </div>
                        </div> -->
        <!-- Modal footer -->
        <!-- <div
                            class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <button type="submit"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Create</button>
                            <button type="button" @click="handleCloseEditModal"
                                class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> -->
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { defineProps, ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { useNotify } from '@/composables/useNotify';
import { TrendingUpDown } from 'lucide-vue-next';

const notify = useNotify();
const users = ref<any>({});
const products = ref<Array<{ product_id: string; product_name: string; product_code: string; product_description: string; product_price: number; product_quantity: number; product_image: string | File }>>([]);
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
    fetchTableData();
    // fetchRoles();
}

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

const newProduct = ref<{ product_id?: string; product_name: string; product_code: string; product_description: string; product_price: number; product_quantity: number; product_image: string | File }>({
    product_id: '',
    product_name: '',
    product_code: '',
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
    if (newProduct.value.product_id) formData.append('product_id', newProduct.value.product_id);
    formData.append('product_name', newProduct.value.product_name);
    formData.append('product_code', newProduct.value.product_code);
    formData.append('product_description', newProduct.value.product_description);
    formData.append('product_price', newProduct.value.product_price.toString());
    formData.append('product_quantity', newProduct.value.product_quantity.toString());
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
                product_code: '',
                product_description: '',
                product_price: 0,
                product_quantity: 0,
                product_image: '',
            };
            handleCloseModal();
            handleCloseEditModal();
            reload();
            notify(response.data.message, "success");
        }
    } catch (error) {
        notify("Error creating product", "error");
        console.error('Error:', error);
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
    newUser.value = { id: '', name: '', email: '', password: '', password_confirmation: '', roles: [] };
    if (EditModal.value) {
        EditModal.value.classList.add('hidden');
    }
};

const fetchUserDetails = async (userId: string) => {
    const route_url = route('users.edit', userId);
    try {
        const response = await axios.get(route_url);
        if (response.data.result === true) {
            // Set role data for editing
            newUser.value = {
                id: response.data.data.id,
                name: response.data.data.name,
                email: response.data.data.email,
                password: response.data.data.password ?? null,
                password_confirmation: response.data.data.password ?? null,
                roles: response.data.data.roles
                    .filter((p: any) => p.assigned)
                    .map((p: any) => p.id),
            };

            handleOpenEditModal();
        }
    } catch (error) {
        notify("Error fetching role details", "error");
        console.error('Error:', error);
    }
};


const handleDeleteUser = async (userId: string) => {
    const route_url = route('users.destroy', userId);
    if (confirm('Are you sure you want to delete this user?')) {
        try {
            const response = await axios.post(route_url);
            if (response.status === 200) {
                notify("User deleted successfully", "success");
                reload();
            }
        } catch (error) {
            notify("Error deleting user", "error");
            console.error('Error:', error);
        }
    }
};

// Handle image file input change
const onImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        // You may want to upload the file or convert it to base64, here we just store the file object
        newProduct.value.product_image = file;
    }
};

// const AddPermissionModal = ref<HTMLElement | null>(null);
// const permissionFormRef = ref<HTMLFormElement | null>(null);
// const newPermission = ref({ name: '' });

// const handleOpenPermissionModal = () => {
//     if (AddPermissionModal.value) {
//         AddPermissionModal.value.classList.remove('hidden');
//     }
// };
// const handleClosePermissionModal = () => {
//     if (AddPermissionModal.value) {
//         AddPermissionModal.value.classList.add('hidden');
//     }
// };

// const submitPermission = async () => {
//     const route_url = route('permissions.store');
//     try {
//         const response = await axios.post(route_url, newPermission.value);
//         if (response.data.result == true) {
//             if (permissionFormRef.value) permissionFormRef.value.reset();
//             newPermission.value = { name: '' };
//             handleClosePermissionModal();
//             notify("Permission created successfully", "success");
//         } else {
//             notify(response.data.message, "error");
//             console.error('Error creating permission:', response.data);
//         }
//     } catch (error) {
//         console.error('Error:', error);
//     }
// };
</script>