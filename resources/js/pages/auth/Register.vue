<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

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
                    <p class="mt-2 text-lg text-gray-600">Create your account and join us today.</p>
                </div>

                <!-- Register Form -->
                <form @submit.prevent="submit" class="flex flex-col gap-6">
                    <div class="grid gap-6">
                        <!-- Name Input -->
                        <div class="mb-2">
                            <label for="name" class="block mb-2 text-sm font-medium text-black">Full Name</label>
                            <input
                                type="text"
                                id="name"
                                class="bg-white border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="John Doe"
                                v-model="form.name"
                                required
                            />
                            <InputError :message="form.errors.name" />
                        </div>

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

                        <!-- Confirm Password Input -->
                        <div class="mb-2">
                            <label for="password_confirmation" class="block mb-2 text-sm font-medium text-black">Confirm Password</label>
                            <input
                                type="password"
                                id="password_confirmation"
                                class="bg-white border border-gray-300 text-black text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="•••••••••"
                                v-model="form.password_confirmation"
                                required
                            />
                            <InputError :message="form.errors.password_confirmation" />
                        </div>

                        <!-- Submit Button -->
                        <Button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" :disabled="form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Create account
                        </Button>
                    </div>

                    <!-- Log In Link -->
                    <div class="text-center text-sm text-muted-foreground">
                        Already have an account?
                        <TextLink :href="route('login')" :tabindex="6">Log in</TextLink>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
