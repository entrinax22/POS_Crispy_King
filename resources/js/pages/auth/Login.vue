<template>
    <div class="grid h-screen grid-cols-1 md:grid-cols-2">
        <!-- Left Column -->
        <div class="hidden bg-cover bg-center md:block" style="background-image: url('/CrispyKing.png')">
            <!-- Add any additional content or leave empty for just the background -->
        </div>

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
                            <label for="email" class="mb-2 block text-sm font-medium text-black">Email address</label>
                            <input
                                type="email"
                                id="email"
                                class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-black focus:border-blue-500 focus:ring-blue-500"
                                placeholder="john.doe@company.com"
                                v-model="form.email"
                                required
                            />
                            <InputError :message="form.errors.email" />
                        </div>

                        <!-- Password Input -->
                        <div class="mb-2">
                            <label for="password" class="mb-2 block text-sm font-medium text-black">Password</label>
                            <input
                                type="password"
                                id="password"
                                class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-black focus:border-blue-500 focus:ring-blue-500"
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
                        <Button
                            type="submit"
                            class="me-2 mb-2 rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            :tabindex="4"
                            :disabled="form.processing"
                        >
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Log in
                        </Button>

                        <!-- Google Login Button -->
                        <button
                            @click="googleLogin"
                            type="button"
                            class="flex items-center justify-center gap-3 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="h-5 w-5" />
                            <span>Continue with Google</span>
                        </button>
                    </div>

                    <!-- Sign Up Link -->
                    <div class="text-muted-foreground text-center text-sm">
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
import { useForm } from '@inertiajs/vue3';
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

const googleLogin = () => {
    window.location.href = route('google.login');
};
</script>
