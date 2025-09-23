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
                    <p class="mt-2 text-lg text-gray-600">Welcome back! Please log in to your account.</p>
                </div>

                <!-- Login Form -->
                <form @submit.prevent="submit" class="flex flex-col gap-6">
                    <div class="grid gap-6">
                        <!-- Email Input -->
                        <div class="mb-2">
                            <label for="email" class="mb-2 block text-sm font-medium text-black"> Email address </label>
                            <input
                                type="email"
                                id="email"
                                v-model="form.email"
                                placeholder="john.doe@company.com"
                                required
                                class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-black focus:border-blue-500 focus:ring-blue-500"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Password Input -->
                        <div class="mb-2">
                            <label for="password" class="mb-2 block text-sm font-medium text-black"> Password </label>
                            <input
                                type="password"
                                id="password"
                                v-model="form.password"
                                placeholder="•••••••••"
                                required
                                class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-black focus:border-blue-500 focus:ring-blue-500"
                            />
                            <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <label for="remember" class="flex items-center space-x-2">
                                <input
                                    id="remember"
                                    type="checkbox"
                                    v-model="form.remember"
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                />
                                <span class="text-black">Remember me</span>
                            </label>
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
                            Log in
                        </button>

                        <!-- Google Login -->
                        <button
                            type="button"
                            @click="googleLogin"
                            class="flex items-center justify-center gap-3 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="h-5 w-5" />
                            <span>Continue with Google</span>
                        </button>
                    </div>

                    <!-- Sign Up -->
                    <div class="text-center text-sm text-gray-600">
                        Don’t have an account?
                        <a :href="route('register')" class="font-medium text-blue-600 hover:underline"> Sign up </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const googleLogin = () => {
    window.location.href = route('google.login');
};
</script>
