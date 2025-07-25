<template>
    <Head title="Crispy King | Tables" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="text-2xl font-bold">Tables</h1>

            <!-- Tables Table -->
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
                        @click="handleOpenModal()"
                    >
                        Add Table
                    </button>
                </div>
            </div>
            <BaseTable
                :columns="columns"
                :rows="tables.data ?? []"
                :pagination="tables.pagination ?? {}"
                uniqueKey="table_id"
                @page-change="fetchTableData"
            >
                <template #count="{ index }">
                    {{ tables.pagination.per_page * (tables.pagination.current_page - 1) + index + 1 }}
                </template>

                <template #actions="{ row }">
                    <button
                        class="mr-2 rounded bg-yellow-500 px-3 py-1 font-medium text-white hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-300"
                        @click="fetchTableDetails(row.table_id)"
                    >
                        Edit
                    </button>
                    <button
                        class="rounded bg-red-600 px-3 py-1 font-medium text-white hover:bg-red-700 focus:ring-2 focus:ring-red-300"
                        @click="handleDeleteTable(row.table_id)"
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
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Create Table</h2>
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
                    <form ref="formRef" @submit.prevent="submitTable">
                        <div class="grid grid-cols-1 gap-4 space-y-4 p-4 md:grid-cols-2 md:p-5">
                            <div class="mb-4">
                                <label for="table-number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Table Number</label>
                                <input
                                    type="text"
                                    id="table_number"
                                    v-model="newTable.table_number"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Table Status</label>
                                <select
                                    id="status"
                                    v-model="newTable.status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                >
                                    <option value="" disabled>Select status</option>
                                    <option value="available">Available</option>
                                    <option value="reserved">Reserved</option>
                                </select>
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
                    <form ref="formRef" @submit.prevent="submitEditTable">
                        <div class="grid grid-cols-1 gap-4 space-y-4 p-4 md:grid-cols-2 md:p-5">
                            <input type="hidden" v-model="editTable.table_id" />
                            <div class="mb-4">
                                <label for="table-number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Table Number</label>
                                <input
                                    type="text"
                                    id="table_number"
                                    v-model="editTable.table_number"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Table Status</label>
                                <select
                                    id="status"
                                    v-model="editTable.status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                >
                                    <option value="" disabled>Select status</option>
                                    <option value="available">Available</option>
                                    <option value="reserved">Reserved</option>
                                </select>
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
    { label: 'Table Number', key: 'table_number' },
    { label: 'Status', key: 'status' },
    { label: 'Actions', key: 'actions' },
];

const notify = useNotify();

const tables = ref<{
    data: Array<{
        table_id: string;
        table_number: number;
        status: string;
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
        const response = await axios.get(route('tables.list', { page, search: search.value }));
        if (response.data.result === true) {
            tables.value = response.data;
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
        title: 'Tables',
        href: route('tables.index'),
    },
];

const visible = ref(false);

const newTable = ref<{
    table_id?: string;
    table_number: number;
    status: string;
}>({
    table_id: '',
    table_number: 0,
    status: 'available',
});

// Table Add Modal logic
const AddModal = ref<HTMLElement | null>(null);
const formRef = ref<HTMLFormElement | null>(null);

const handleOpenModal = () => {
    visible.value = true;
    if (AddModal.value) {
        AddModal.value.classList.remove('hidden');
    }
};

const handleCloseModal = () => {
    visible.value = false;
    newTable.value = {
        table_id: '',
        table_number: 0,
        status: 'Available', // Reset to default
    };
    if (AddModal.value) {
        AddModal.value.classList.add('hidden');
    }
};

const submitTable = async () => {
    const route_url = route('tables.store');
    const formData = new FormData();
    if (newTable.value.table_id && newTable.value.table_id !== '') {
        formData.append('table_id', newTable.value.table_id);
    }
    formData.append('table_number', newTable.value.table_number.toString());
    formData.append('status', newTable.value.status);
    try {
        const response = await axios.post(route_url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        if (response.data.result == true) {
            if (formRef.value) formRef.value.reset();
            newTable.value = {
                table_id: '',
                table_number: 0,
                status: 'Available', // Reset to default
            };
            handleCloseModal();
            reload();
            notify(response.data.message, 'success');
        }
    } catch (error: any) {
        notify('Error creating/updating table', 'error');
    }
};

// Table Edit Modal logic
const EditModal = ref<HTMLElement | null>(null);
const editTable = ref<{ table_id?: string; table_number?: number; status?: string }>({ table_id: '', table_number: 0, status: 'Available' });

const handleOpenEditModal = () => {
    visible.value = true;
    if (EditModal.value) {
        EditModal.value.classList.remove('hidden');
    }
};

const handleCloseEditModal = () => {
    visible.value = false;
    editTable.value = { table_id: '', table_number: 0 };
    if (EditModal.value) {
        EditModal.value.classList.add('hidden');
    }
};

const fetchTableDetails = async (tableId: string) => {
    const route_url = route('tables.edit', tableId);
    try {
        const response = await axios.get(route_url);
        if (response.data.result === true) {
            editTable.value = {
                table_id: response.data.data.table_id,
                table_number: response.data.data.table_number,
                status: response.data.data.status,
            };
            handleOpenEditModal();
        }
    } catch (error: any) {
        notify('Error fetching table details', 'error');
        console.error('Error:', error.message);
    }
};

const submitEditTable = async () => {
    const route_url = route('tables.store');
    const payload = {
        table_id: editTable.value.table_id,
        table_number: editTable.value.table_number,
        status: editTable.value.status,
    };
    try {
        const response = await axios.post(route_url, payload);
        if (response.data.result === true) {
            handleCloseEditModal();
            reload();
            notify(response.data.message, 'success');
        }
    } catch (error: any) {
        notify('Error updating table', 'error');
        console.error('Error:', error.message);
    }
};

const handleDeleteTable = async (tableId: string) => {
    const route_url = route('tables.destroy', tableId);
    if (confirm('Are you sure you want to delete this table?')) {
        try {
            const response = await axios.post(route_url);
            if (response.status === 200) {
                notify('Table deleted successfully', 'success');
                reload();
            }
        } catch (error) {
            notify('Error deleting table', 'error');
            console.error('Error:', error);
        }
    }
};
</script>
