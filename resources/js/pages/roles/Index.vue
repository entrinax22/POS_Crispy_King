<template>

    <Head title="Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="text-2xl font-bold">Roles & Permissions</h1>

            <!-- Roles Table -->
            <div class="mr-4 flex justify-end">
                <button
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" @click="handleOpenModal()">Create
                    Role
                </button>
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
                        <tr v-for="role in roles" :key="role.id"
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{
                                role.name }}</td>
                            <td class="px-6 py-4">
                                <ul>
                                    <li v-for="permission in role.permissions" :key="permission.id">
                                        {{ permission.name }}
                                    </li>
                                </ul>
                            </td>
                            <td class="px-6 py-4">
                                <a :href="route('roles.index')" class="text-blue-600 hover:text-blue-900">Edit</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div ref="AddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-300 bg-opacity-20 hidden">
            <div class="bg-white p-6 rounded shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-4">Create Role</h2>
            <form @submit.prevent="submit">
                <div class="mb-4">
                <label for="role-name" class="block text-gray-700">Role Name</label>
                <input type="text" id="role-name" v-model="newRole.name"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                    required />
                </div>
                <div class="mb-4">
                <label for="permissions" class="block text-gray-700">Permissions</label>
                <select id="permissions" v-model="newRole.permissions" multiple
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <option v-for="permission in permissions" :key="permission.id" :value="permission.id">
                    {{ permission.name }}
                    </option>
                </select>
                </div>
                <div class="flex justify-end">
                <button type="button" @click="handleCloseModal()"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 mr-2">Cancel</button>
                <button type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Create</button>
                </div>
            </form>
            </div>
        </div>
        </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: route('roles.index'),
    },
];

defineProps<{
    roles: Array<{
        id: number;
        name: string;
        permissions: Array<{ id: number; name: string }>;
    }>;
    permissions: Array<{ id: number; name: string }>;
}>();

const visible = ref(false);

const newRole = ref({
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
    
const submit = async () => {
    const route_url = route('roles.store');

    try {
        const response = await axios.post(route_url, newRole.value);

        if (response.status === 200) {
            console.log('Role created successfully:', response.data);
            handleCloseModal(); // Close the modal after successful creation
        } else {
            console.error('Error creating role:', response.data);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

</script>