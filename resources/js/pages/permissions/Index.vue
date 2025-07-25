<template>
    <Head title="Crispy King | Permissions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="text-2xl font-bold">Permissions</h1>

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
                                placeholder="Search permissions..."
                            />
                        </div>
                    </form>
                    <button
                        class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium whitespace-nowrap text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        @click="handleOpenPermissionModal()"
                    >
                        Create Permission
                    </button>
                </div>
            </div>
            <BaseTable
                :columns="columns"
                :rows="permissions.data ?? []"
                :pagination="permissions.pagination ?? {}"
                uniqueKey="id"
                @page-change="fetchTableData"
            >
                <template #count="{ index }">
                    {{ permissions.pagination.per_page * (permissions.pagination.current_page - 1) + index + 1 }}
                </template>
                <template #actions="{ row }">
                    <button
                        class="mr-2 rounded bg-yellow-500 px-3 py-1 font-medium text-white hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-300"
                        @click="editPermission(row.id)"
                    >
                        Edit
                    </button>
                    <button
                        class="rounded bg-red-600 px-3 py-1 font-medium text-white hover:bg-red-700 focus:ring-2 focus:ring-red-300"
                        @click="handleDeletePermission(row.id)"
                    >
                        Delete
                    </button>
                </template>
            </BaseTable>
        </div>

        <!-- Create Permission Modal -->
        <div
            ref="AddPermissionModal"
            tabindex="-1"
            class="fixed inset-0 z-50 flex hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-x-hidden overflow-y-auto backdrop-blur-sm md:inset-0"
        >
            <div class="relative mx-auto my-auto flex max-h-full w-full max-w-2xl items-center justify-center p-4">
                <div class="relative rounded-lg bg-white shadow-sm dark:bg-gray-700">
                    <div class="flex items-center justify-between rounded-t border-b border-gray-200 p-4 md:p-5 dark:border-gray-600">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Create Permission</h2>
                        <button
                            @click="handleClosePermissionModal"
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
                    <form ref="permissionFormRef" @submit.prevent="submitPermission">
                        <div class="space-y-4 p-4 md:p-5">
                            <div class="mb-4">
                                <label for="permission-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >Permission Name</label
                                >
                                <input
                                    type="text"
                                    id="permission-name"
                                    v-model="newPermission.name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                        </div>
                        <div class="flex items-center rounded-b border-t border-gray-200 p-4 md:p-5 dark:border-gray-600">
                            <button
                                type="submit"
                                class="rounded-lg bg-blue-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            >
                                Create
                            </button>
                            <button
                                type="button"
                                @click="handleClosePermissionModal"
                                class="ms-3 rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Permission Modal -->
        <div
            ref="EditPermissionModal"
            tabindex="-1"
            class="fixed inset-0 z-50 flex hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-x-hidden overflow-y-auto backdrop-blur-sm md:inset-0"
        >
            <div class="relative mx-auto my-auto flex max-h-full w-full max-w-2xl items-center justify-center p-4">
                <div class="relative rounded-lg bg-white shadow-sm dark:bg-gray-700">
                    <div class="flex items-center justify-between rounded-t border-b border-gray-200 p-4 md:p-5 dark:border-gray-600">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Edit Permission</h2>
                        <button
                            @click="handleCloseEditPermissionModal"
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
                    <form ref="permissionFormRef" @submit.prevent="submitPermission">
                        <input type="hidden" id="permission-id" v-model="newPermission.id" />
                        <div class="space-y-4 p-4 md:p-5">
                            <div class="mb-4">
                                <label for="permission-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >Permission Name</label
                                >
                                <input
                                    type="text"
                                    id="permission-name"
                                    v-model="newPermission.name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                        </div>
                        <div class="flex items-center rounded-b border-t border-gray-200 p-4 md:p-5 dark:border-gray-600">
                            <button
                                type="submit"
                                class="rounded-lg bg-blue-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            >
                                Save
                            </button>
                            <button
                                type="button"
                                @click="handleClosePermissionModal"
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
    { key: 'count', label: '#' },
    { key: 'name', label: 'Name' },
    { key: 'actions', label: 'Actions' },
];

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
        const response = await axios.get(route('permissions.list', { page, search: search.value }));
        if (response.data.result === true) {
            permissions.value = response.data;
        }
    } catch (error) {
        console.error('Error fetching permissions:', error);
    }
};

const reload = () => {
    fetchTableData();
};

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
const newPermission = ref<{ id?: string; name: string }>({
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
    const payload = newPermission.value.id ? { id: newPermission.value.id, name: newPermission.value.name } : { name: newPermission.value.name };
    try {
        const response = await axios.post(route_url, payload);
        if (response.data.result == true) {
            if (permissionFormRef.value) permissionFormRef.value.reset();
            handleClosePermissionModal();
            handleCloseEditPermissionModal();
            reload();
            notify(response.data.message, 'success');
        } else {
            notify(response.data.message, 'error');
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
            notify(response.data.message, 'error');
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
                notify(response.data.message, 'success');
                reload();
            }
        } catch (error) {
            notify('Error deleting permission', 'error');
            console.error('Error:', error);
        }
    }
};
</script>
