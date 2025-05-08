<template>
    <div class="grid grid-cols-1 md:grid-cols-2 h-screen">
        <!-- Left Column -->
        <div class="hidden md:block bg-cover bg-center" style="background-image: url('/CrispyKing.png');">
            <!-- Add any additional content or leave empty for just the background -->
        </div>

        <!-- Right Column -->
        <div class="flex items-center justify-center p-8 bg-white">
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
                            <label for="email" class="block mb-2 text-sm font-medium text-black">Email address</label>
                            <input
                                type="email"
                                id="email"
                                class="bg-white border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="john.doe@company.com"
                                v-model="form.email"
                                required
                            />
                            <InputError :message="form.errors.email" />
                        </div>

                        <!-- Password Input -->
                        <div class="mb-2">
                            <label for="password" class="block mb-2 text-sm font-medium text-black">Password</label>
                            <input
                                type="password"
                                id="password"
                                class="bg-white border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="•••••••••"
                                v-model="form.password"
                                required
                            />
                            <InputError :message="form.errors.password" />
                        </div>

                        <!-- Remember Me Checkbox -->
                        <div class="flex items-center justify-between">
                            <Label for="remember" class="flex items-center space-x-3">
                                <Checkbox id="remember" v-model="form.remember" :tabindex="3" />
                                <span class="text-black">Remember me</span>
                            </Label>
                        </div>

                        <!-- Submit Button -->
                        <Button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" :tabindex="4" :disabled="form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Log in
                        </Button>
                    </div>

                    <!-- Sign Up Link -->
                    <div class="text-center text-sm text-muted-foreground">
                        Don't have an account?
                        <TextLink :href="route('register')" :tabindex="5">Sign up</TextLink>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

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
</script>