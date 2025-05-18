<template>

    <Head title="Permissions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="text-2xl font-bold">Permissions</h1>

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
                            <input type="search" id="default-search"
                                v-model="search"
                                class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search permissions..." />
                        </div>
                    </form>
                    <button
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 whitespace-nowrap"
                        @click="handleOpenPermissionModal()">Create Permission
                    </button>
                </div>
            </div>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 bg-white">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Name</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="permission in permissions.data" :key="permission.id"
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-left">
                                {{ permission.name }}</td>
                            <td class="px-6 py-4 text-left">
                                <button
                                    class="text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-300 font-medium rounded px-3 py-1 mr-2" @click="editPermission(permission.id)">Edit</button>
                                <button
                                    class="text-white bg-red-600 hover:bg-red-700 focus:ring-2 focus:ring-red-300 font-medium rounded px-3 py-1" @click="handleDeletePermission(permission.id)">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot v-if="permissions.pagination">
                        <tr>
                            <td colspan="1" class="px-6 py-4">
                                <span class="text-sm text-gray-600">
                                    Total: {{ permissions.pagination.total }} permissions
                                </span>
                            </td>
                            <td colspan="2" class="px-6 py-4">
                                <div class="flex justify-end items-center gap-2">
                                    <button :disabled="permissions.pagination.current_page === 1"
                                        @click="fetchTableData(permissions.pagination.current_page - 1)"
                                        class="px-2 py-1 rounded border bg-gray-200 hover:bg-gray-300 disabled:opacity-50"
                                        aria-label="Previous page">
                                        &laquo;
                                    </button>
                                    <template v-for="page in Math.min(permissions.pagination.last_page, 5)" :key="page">
                                        <button
                                            v-if="page === 1 || page === permissions.pagination.last_page || Math.abs(page - permissions.pagination.current_page) <= 1"
                                            :class="[
                                                'px-3 py-1 rounded border',
                                                page === permissions.pagination.current_page
                                                    ? 'bg-blue-600 text-white'
                                                    : 'bg-gray-200 hover:bg-gray-300'
                                            ]" @click="fetchTableData(page)"
                                            :disabled="page === permissions.pagination.current_page">
                                            {{ page }}
                                        </button>
                                        <span
                                            v-else-if="page === permissions.pagination.current_page - 2 || page === permissions.pagination.current_page + 2"
                                            class="px-2">...</span>
                                    </template>
                                    <button
                                        :disabled="permissions.pagination.current_page === permissions.pagination.last_page"
                                        @click="fetchTableData(permissions.pagination.current_page + 1)"
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

        <!-- Create Permission Modal -->
        <div ref="AddPermissionModal" tabindex="-1" aria-hidden="true"
            class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm  overflow-y-auto overflow-x-hidden w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full flex items-center justify-center mx-auto my-auto">
                <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                    <div
                        class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Create Permission</h2>
                        <button @click="handleClosePermissionModal" type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <form ref="permissionFormRef" @submit.prevent="submitPermission">
                        <div class="p-4 md:p-5 space-y-4">
                            <div class="mb-4">
                                <label for="permission-name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Permission
                                    Name</label>
                                <input type="text" id="permission-name" v-model="newPermission.name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required />
                            </div>
                        </div>
                        <div
                            class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <button type="submit"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Create</button>
                            <button type="button" @click="handleClosePermissionModal"
                                class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Permission Modal -->
        <div ref="EditPermissionModal" tabindex="-1" aria-hidden="true"
            class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm  overflow-y-auto overflow-x-hidden w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full flex items-center justify-center mx-auto my-auto">
                <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                    <div
                        class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Edit Permission</h2>
                        <button @click="handleCloseEditPermissionModal" type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <form ref="permissionFormRef" @submit.prevent="submitPermission">
                        <input type="hidden" id="permission-id" v-model="newPermission.id" />
                        <div class="p-4 md:p-5 space-y-4">
                            <div class="mb-4">
                                <label for="permission-name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Permission
                                    Name</label>
                                <input type="text" id="permission-name" v-model="newPermission.name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required />
                            </div>
                        </div>
                        <div
                            class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <button type="submit"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Create</button>
                            <button type="button" @click="handleClosePermissionModal"
                                class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
const permissions = ref(<any>{});
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
        const response = await axios.get(route('permissions.list', { page , search: search.value }));
        if (response.data.result === true) {
            permissions.value = response.data;
        }
    } catch (error) {
        console.error('Error fetching permissions:', error);
    }
};

const reload = () => {
    fetchTableData();
}

onMounted(() => {
    fetchTableData(1);
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: route('roles.index'),
    },
];

const visible = ref(false);

const AddPermissionModal = ref<HTMLElement | null>(null);
const permissionFormRef = ref<HTMLFormElement | null>(null);
const newPermission = ref<{ id?: string; name: string; }>({
    id: '',
    name: '',
});

const handleOpenPermissionModal = () => {
    if (AddPermissionModal.value) {
        AddPermissionModal.value.classList.remove('hidden');
    }
};

const handleClosePermissionModal = () => {
    if (AddPermissionModal.value) {
        AddPermissionModal.value.classList.add('hidden');
    }
};

const submitPermission = async () => {
    const route_url = route('permissions.store');
    const payload = newPermission.value.id
        ? { id: newPermission.value.id, name: newPermission.value.name }
        : { name: newPermission.value.name };
    try {
        const response = await axios.post(route_url, payload);
        if (response.data.result == true) {
            if (permissionFormRef.value) permissionFormRef.value.reset();
            handleClosePermissionModal();
            handleCloseEditPermissionModal();
            reload();
            notify(response.data.message, "success");
        } else {
            notify(response.data.message, "error");
            console.error('Error creating permission:', response.data);
        }
    } catch (error) {
        console.error('Error:', error);
    }
};

const EditPermissionModal = ref<HTMLElement | null>(null);

const handleOpenEditPermissionModal = () => {
    if (EditPermissionModal.value) {
        EditPermissionModal.value.classList.remove('hidden');
    }
};

const handleCloseEditPermissionModal = () => {
    if (EditPermissionModal.value) {
        EditPermissionModal.value.classList.add('hidden');
    }
};

const editPermission = async (permissionId: string) => {
    const route_url = route('permissions.edit', permissionId);
    try {
        const response = await axios.get(route_url);
        if (response.data.result == true) {
            newPermission.value = {
                id: response.data.data.id || '',
                name: response.data.data.name || '',
            };
            handleOpenEditPermissionModal();
        } else {
            notify(response.data.message, "error");
            console.error('Error fetching permission:', response.data);
        }
    } catch (error) {
        console.error('Error:', error);
    }
};

const handleDeletePermission = async (permissionId: string) => {
    const route_url = route('permissions.destroy', permissionId);
    if (confirm('Are you sure you want to delete this permission?')) {
        try {
            const response = await axios.post(route_url);
            if (response.data.result === true) {
                notify(response.data.message, "success");
                reload();
            }
        } catch (error) {
            notify("Error deleting permission", "error");
            console.error('Error:', error);
        }
    }
};

</script>