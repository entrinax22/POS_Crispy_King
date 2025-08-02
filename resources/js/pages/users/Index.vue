<template>
    <Head title="Crispy King | Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="text-2xl font-bold">Users</h1>

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
                                placeholder="Search user..."
                            />
                        </div>
                    </form>
                    <button
                        class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium whitespace-nowrap text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        @click="handleOpenModal()"
                    >
                        Add User
                    </button>
                </div>
            </div>
            <BaseTable
                :columns="columns"
                :rows="users.data ?? []"
                :pagination="users.pagination ?? {}"
                uniqueKey="user_id"
                @page-change="fetchTableData"
            >
                <template #count="{ index }">
                    {{ users.pagination.per_page * (users.pagination.current_page - 1) + index + 1 }}
                </template>
                <template #roles="{ row }">
                    {{ row.roles.assigned.map((role: any) => role.name).join(', ') || 'No roles assigned' }}
                </template>
                <template #actions="{ row }">
                    <button
                        class="mr-2 rounded bg-yellow-500 px-3 py-1 font-medium text-white hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-300"
                        @click="fetchUserDetails(row.id)"
                    >
                        Edit
                    </button>
                    <button
                        class="rounded bg-red-600 px-3 py-1 font-medium text-white hover:bg-red-700 focus:ring-2 focus:ring-red-300"
                        @click="handleDeleteUser(row.id)"
                    >
                        Delete
                    </button>
                </template>
            </BaseTable>
        </div>

        <!-- Add Modal Background -->
        <div
            ref="AddModal"
            tabindex="-1"
            aria-hidden="true"
            class="fixed inset-0 z-50 flex hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-x-hidden overflow-y-auto backdrop-blur-sm md:inset-0"
        >
            <div class="relative mx-auto my-auto flex max-h-full w-full max-w-2xl items-center justify-center p-4">
                <!-- Modal content -->
                <div class="relative rounded-lg bg-white shadow-sm dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between rounded-t border-b border-gray-200 p-4 md:p-5 dark:border-gray-600">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Create User</h2>
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
                    <form ref="formRef" @submit.prevent="submitUser">
                        <div class="space-y-4 p-4 md:p-5">
                            <div class="mb-4">
                                <label for="add-user-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">User Name</label>
                                <input
                                    type="text"
                                    id="add-user-name"
                                    v-model="newUser.name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                            <div class="mb-4">
                                <label for="add-email-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">User Email</label>
                                <input
                                    type="text"
                                    id="add-email-name"
                                    v-model="newUser.email"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                                <input
                                    type="password"
                                    id="password"
                                    v-model="newUser.password"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                            <div class="mb-4">
                                <label for="confirm-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >Confirm Password</label
                                >
                                <input
                                    type="password"
                                    id="confirm-password"
                                    v-model="newUser.password_confirmation"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                                <span
                                    v-if="newUser.password_confirmation && newUser.password !== newUser.password_confirmation"
                                    class="text-xs text-red-500"
                                >
                                    Passwords do not match.
                                </span>
                            </div>
                            <div class="mb-4">
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Permissions</label>
                                <div class="grid max-h-48 grid-cols-3 gap-2 overflow-y-auto">
                                    <div v-for="role in roles" :key="role.id" class="m-3 flex items-center">
                                        <input
                                            type="checkbox"
                                            :id="'perm-' + role.id"
                                            :value="role.id"
                                            v-model="newUser.roles"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                        />
                                        <label :for="'perm-' + role.id" class="ml-2 text-gray-700 dark:text-gray-300">{{ role.name }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="flex items-center rounded-b border-t border-gray-200 p-4 md:p-5 dark:border-gray-600">
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
            aria-hidden="true"
            class="fixed inset-0 z-50 flex hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-x-hidden overflow-y-auto backdrop-blur-sm md:inset-0"
        >
            <div class="relative mx-auto my-auto flex max-h-full w-full max-w-2xl items-center justify-center p-4">
                <!-- Modal content -->
                <div class="relative rounded-lg bg-white shadow-sm dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between rounded-t border-b border-gray-200 p-4 md:p-5 dark:border-gray-600">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Edit User</h2>
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
                    <form ref="formRef" @submit.prevent="submitUser">
                        <div class="space-y-4 p-4 md:p-5">
                            <input type="hidden" id="role-id" v-model="newUser.id" />
                            <div class="mb-4">
                                <label for="edit-user-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">User Name</label>
                                <input
                                    type="text"
                                    id="edit-user-name"
                                    v-model="newUser.name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                            <div class="mb-4">
                                <label for="edit-user-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">User Email</label>
                                <input
                                    type="text"
                                    id="edit-user-email"
                                    v-model="newUser.email"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>
                            <div class="mb-4">
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Roles</label>
                                <div class="grid max-h-48 grid-cols-3 gap-2 overflow-y-auto">
                                    <div v-for="role in roles" :key="role.id" class="m-3 flex items-center">
                                        <input
                                            type="checkbox"
                                            :id="'edit-perm-' + role.id"
                                            :value="role.id"
                                            v-model="newUser.roles"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white"
                                        />
                                        <label :for="'edit-perm-' + role.id" class="ml-2 text-gray-700 dark:text-gray-300">{{ role.name }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="flex items-center rounded-b border-t border-gray-200 p-4 md:p-5 dark:border-gray-600">
                            <button
                                type="submit"
                                class="rounded-lg bg-blue-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            >
                                Create
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
    { key: 'count', label: '#', sortable: false },
    { key: 'name', label: 'Name', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'roles', label: 'Roles', sortable: false },
    { key: 'actions', label: 'Actions', sortable: false },
];

const notify = useNotify();
const users = ref<any>({});
const roles = ref<any[]>([]);

const newUser = ref<{ id?: string; name: string; email: string; password: string; password_confirmation: string; roles: string[] }>({
    id: '',
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    roles: [],
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
        const response = await axios.get(route('users.list', { page, search: search.value }));
        if (response.data.result === true) {
            users.value = response.data;
        }
    } catch (error) {
        console.error('Error fetching users:', error);
    }
};

const fetchRoles = async () => {
    try {
        const response = await axios.get(route('roles.list'));
        if (response.data.result === true) {
            roles.value = response.data.data;
        }
    } catch (error) {
        console.error('Error fetching permissions:', error);
    }
};

const reload = () => {
    fetchTableData();
    fetchRoles();
};

onMounted(() => {
    fetchTableData(1);
    fetchRoles();
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: route('users.index'),
    },
];

const visible = ref(false);

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

const submitUser = async () => {
    const route_url = route('users.store');
    const payload = newUser.value.id
        ? {
              id: newUser.value.id,
              name: newUser.value.name,
              email: newUser.value.email,
              password: newUser.value.password ?? null,
              password_confirmation: newUser.value.password_confirmation ?? null,
              roles: newUser.value.roles.map((id) => ({ id })),
          }
        : {
              name: newUser.value.name,
              email: newUser.value.email,
              password: newUser.value.password ?? null,
              password_confirmation: newUser.value.password_confirmation ?? null,
              roles: newUser.value.roles.map((id) => ({ id })),
          };
    try {
        const response = await axios.post(route_url, payload);

        if (response.data.result == true) {
            // console.log('Role created successfully:', response.data);
            if (formRef.value) formRef.value.reset();
            newUser.value = { name: '', email: '', password: '', password_confirmation: '', roles: [] };
            handleCloseModal();
            handleCloseEditModal();
            reload();
            notify(response.data.message, 'success');
        }
    } catch (error) {
        notify('Error creating user', 'error');
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
            const userData = response.data.data;

            // Encrypted IDs — store directly as-is
            newUser.value = {
                id: userData.id, // already encrypted
                name: userData.name,
                email: userData.email,
                password: '',
                password_confirmation: '',
                roles: userData.roles.assigned.map((role: any) => role.id), // encrypted IDs
            };

            // Set available roles — already encrypted
            roles.value = userData.roles.available;
            handleOpenEditModal();
        }
    } catch (error) {
        notify('Error fetching role details', 'error');
        console.error('Error:', error);
    }
};

const handleDeleteUser = async (userId: string) => {
    const route_url = route('users.destroy', userId);
    if (confirm('Are you sure you want to delete this user?')) {
        try {
            const response = await axios.post(route_url);
            if (response.status === 200) {
                notify('User deleted successfully', 'success');
                reload();
            }
        } catch (error) {
            notify('Error deleting user', 'error');
            console.error('Error:', error);
        }
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
