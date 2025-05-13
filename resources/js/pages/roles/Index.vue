<template>

    <Head title="Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="text-2xl font-bold">Roles</h1>

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
                        @click="handleOpenModal()">Create Role
                    </button>
                </div>
            </div>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Role</th>
                            <th scope="col" class="px-6 py-3">Permissions</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="role in roles.data" :key="role.id"
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{
                                role.name }}</td>
                            <td class="px-6 py-4">
                                <ul>
                                    <li v-for="(permission, index) in role.permissions" :key="index">
                                        {{ permission }}
                                    </li>
                                </ul>
                            </td>
                            <td class="px-6 py-4">
                                <button
                                    class="text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-300 font-medium rounded px-3 py-1 mr-2"
                                    @click="fetchRoleDetails(role.id)">
                                    Edit
                                </button>
                                <button
                                    class="text-white bg-red-600 hover:bg-red-700 focus:ring-2 focus:ring-red-300 font-medium rounded px-3 py-1"
                                    @click="handleDeleteRole(role.id)">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot v-if="roles.pagination">
                        <tr>
                            <td colspan="1" class="px-6 py-4">
                                <span class="text-sm text-gray-600">
                                    Total: {{ roles.pagination.total }} roles
                                </span>
                            </td>
                            <td colspan="2" class="px-6 py-4">
                                <div class="flex justify-end items-center gap-2">
                                    <button :disabled="roles.pagination.current_page === 1"
                                        @click="fetchTableData(roles.pagination.current_page - 1)"
                                        class="px-2 py-1 rounded border bg-gray-200 hover:bg-gray-300 disabled:opacity-50"
                                        aria-label="Previous page">
                                        &laquo;
                                    </button>
                                    <template v-for="page in Math.min(roles.pagination.last_page, 5)" :key="page">
                                        <button
                                            v-if="page === 1 || page === roles.pagination.last_page || Math.abs(page - roles.pagination.current_page) <= 1"
                                            :class="[
                                                'px-3 py-1 rounded border',
                                                page === roles.pagination.current_page
                                                    ? 'bg-blue-600 text-white'
                                                    : 'bg-gray-200 hover:bg-gray-300'
                                            ]" @click="fetchTableData(page)"
                                            :disabled="page === roles.pagination.current_page">
                                            {{ page }}
                                        </button>
                                        <span
                                            v-else-if="page === roles.pagination.current_page - 2 || page === roles.pagination.current_page + 2"
                                            class="px-2">...</span>
                                    </template>
                                    <button
                                        :disabled="roles.pagination.current_page === roles.pagination.last_page"
                                        @click="fetchTableData(roles.pagination.current_page + 1)"
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
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Create Role</h2>
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
                    <form ref="formRef" @submit.prevent="submitRole">
                        <div class="p-4 md:p-5 space-y-4">
                            <div class="mb-4">
                                <label for="role-name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role Name</label>
                                <input type="text" id="role-name" v-model="newRole.name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required />
                            </div>
                            <div class="mb-4">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Permissions</label>
                                <div class="grid grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                                    <div v-for="permission in permissions" :key="permission.id"
                                        class="flex items-center m-3">
                                        <input type="checkbox" :id="'perm-' + permission.id" :value="permission.id"
                                            v-model="newRole.permissions"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white" />
                                        <label :for="'perm-' + permission.id"
                                            class="ml-2 text-gray-700 dark:text-gray-300">{{ permission.name }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
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
        <div ref="EditModal" tabindex="-1" aria-hidden="true"
            class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm overflow-y-auto overflow-x-hidden w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full flex items-center justify-center mx-auto my-auto">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                    <!-- Modal header -->
                    <div
                        class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Edit Role</h2>
                        <button @click="handleCloseEditModal" type="button"
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
                    <form ref="formRef" @submit.prevent="submitRole">
                        <div class="p-4 md:p-5 space-y-4">
                            <input type="hidden" id="role-id" v-model="newRole.id" />
                            <div class="mb-4">
                                <label for="role-name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role Name</label>
                                <input type="text" id="role-name" v-model="newRole.name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required />
                            </div>
                            <div class="mb-4">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Permissions</label>
                                <div class="grid grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                                    <div v-for="permission in permissions" :key="permission.id"
                                        class="flex items-center m-3">
                                        <input type="checkbox" :id="'perm-' + permission.id" :value="permission.id"
                                            v-model="newRole.permissions"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white" />
                                        <label :for="'perm-' + permission.id"
                                            class="ml-2 text-gray-700 dark:text-gray-300">{{ permission.name }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <button type="submit"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Create</button>
                            <button type="button" @click="handleCloseEditModal"
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
const roles = ref<any>({});
const permissions = ref<Array<{ id: number; name: string }>>([]);
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
        const response = await axios.get(route('roles.list', { page, search: search.value }));
        if (response.data.result === true) {
            roles.value = response.data;
        }
    } catch (error) {
        console.error('Error fetching roles:', error);
    }
};

const fetchPermissions = async () => {
    try {
        const response = await axios.get(route('permissions.list'));
        if (response.data.result === true) {
            permissions.value = response.data.data;
        }
    } catch (error) {
        console.error('Error fetching permissions:', error);
    }
};

const reload = () => {
    fetchTableData();
    fetchPermissions();
}

onMounted(() => {
    fetchTableData(1);
    fetchPermissions();
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: route('roles.index'),
    },
];

const visible = ref(false);

const newRole = ref<{ id?: string; name: string; permissions: string[] }>({
    id: '',
    name: '',
    permissions: [],
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

const submitRole = async () => {
    const route_url = route('roles.store');
    const payload = {
        id: newRole.value.id,
        name: newRole.value.name,
        permissions: newRole.value.permissions.map((id) => ({ id })),
    };
    try {
        const response = await axios.post(route_url, payload);

        if (response.data.result == true) {
            console.log('Role created successfully:', response.data);
            if (formRef.value) formRef.value.reset();
            newRole.value = { name: '', permissions: [] };
            handleCloseModal();
            handleCloseEditModal();
            reload();
            notify(response.data.message, "success");
        }
    } catch (error) {
        notify("Error creating role", "error");
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
    if (EditModal.value) {
        EditModal.value.classList.add('hidden');
    }
};

const fetchRoleDetails = async (roleId: string) => {
    const route_url = route('roles.edit', roleId);
    try {
        const response = await axios.get(route_url);
        if (response.data.result === true) {
            // Set role data for editing
            newRole.value = {
                id: response.data.data.id,
                name: response.data.data.name,
                permissions: response.data.data.permissions
                    .filter((p: any) => p.assigned)
                    .map((p: any) => p.id),
            };
            // Optionally update permissions list if needed
            permissions.value = response.data.data.permissions.map((p: any) => ({
                id: p.id,
                name: p.name,
            }));
            handleOpenEditModal();
        }
    } catch (error) {
        notify("Error fetching role details", "error");
        console.error('Error:', error);
    }
};


const handleDeleteRole = async (roleId: string) => {
    const route_url = route('roles.destroy', roleId);
    if (confirm('Are you sure you want to delete this role?')) {
        try {
            const response = await axios.post(route_url);
            if (response.status === 200) {
                notify("Role deleted successfully", "success");
                reload();
            }
        } catch (error) {
            notify("Error deleting role", "error");
            console.error('Error:', error);
        }
    }
};

const AddPermissionModal = ref<HTMLElement | null>(null);
const permissionFormRef = ref<HTMLFormElement | null>(null);
const newPermission = ref({ name: '' });

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
    try {
        const response = await axios.post(route_url, newPermission.value);
        if (response.data.result == true) {
            if (permissionFormRef.value) permissionFormRef.value.reset();
            newPermission.value = { name: '' };
            handleClosePermissionModal();
            notify("Permission created successfully", "success");
        } else {
            notify(response.data.message, "error");
            console.error('Error creating permission:', response.data);
        }
    } catch (error) {
        console.error('Error:', error);
    }
};
</script>