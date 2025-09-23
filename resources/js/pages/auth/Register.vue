<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="grid h-screen grid-cols-1 md:grid-cols-2">
        <!-- Left Column -->
        <div class="hidden bg-cover bg-center md:block" style="background-image: url('/CrispyKing.png')"></div>

        <!-- Right Column -->
        <div class="flex items-center justify-center bg-white p-8">
            <div class="w-full max-w-md space-y-8">
                <!-- Welcome Message -->
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-gray-800">Crispy King</h1>
                    <p class="mt-2 text-lg text-gray-600">Create your account and join us today.</p>
                </div>

                <!-- Register Form -->
                <form @submit.prevent="submit" class="flex flex-col gap-6">
                    <div class="grid gap-6">
                        <!-- Name -->
                        <div class="mb-2">
                            <label for="name" class="mb-2 block text-sm font-medium text-black"> Full Name </label>
                            <input
                                type="text"
                                id="name"
                                v-model="form.name"
                                required
                                placeholder="John Doe"
                                class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-black focus:border-blue-500 focus:ring-blue-500"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div class="mb-2">
                            <label for="email" class="mb-2 block text-sm font-medium text-black"> Email address </label>
                            <input
                                type="email"
                                id="email"
                                v-model="form.email"
                                required
                                placeholder="john.doe@company.com"
                                class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-black focus:border-blue-500 focus:ring-blue-500"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Password -->
                        <div class="mb-2">
                            <label for="password" class="mb-2 block text-sm font-medium text-black"> Password </label>
                            <input
                                type="password"
                                id="password"
                                v-model="form.password"
                                required
                                placeholder="•••••••••"
                                class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-black focus:border-blue-500 focus:ring-blue-500"
                            />
                            <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-2">
                            <label for="password_confirmation" class="mb-2 block text-sm font-medium text-black"> Confirm Password </label>
                            <input
                                type="password"
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                required
                                placeholder="•••••••••"
                                class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-black focus:border-blue-500 focus:ring-blue-500"
                            />
                            <p v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-500">
                                {{ form.errors.password_confirmation }}
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex items-center justify-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none disabled:opacity-50"
                        >
                            <svg
                                v-if="form.processing"
                                class="mr-2 h-4 w-4 animate-spin"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Create account
                        </button>
                    </div>

                    <!-- Log In Link -->
                    <div class="text-center text-sm text-gray-600">
                        Already have an account?
                        <a :href="route('login')" class="font-medium text-blue-600 hover:underline"> Log in </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
