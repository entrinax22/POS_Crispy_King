<template>
    <CustomerLayout>
        <div class="mx-auto mt-6 w-full max-w-3xl rounded-2xl bg-white p-6 shadow-lg">
            <!-- Page Title -->
            <h1 class="mb-6 text-2xl font-extrabold text-orange-700">My Account</h1>

            <!-- User Info Card -->
            <div class="mb-6 rounded-xl border border-gray-200 bg-gradient-to-r from-yellow-50 to-orange-50 p-6 shadow">
                <div class="flex items-center gap-4">
                    <img
                        src="https://ui-avatars.com/api/?name=John+Doe"
                        alt="Profile"
                        class="h-20 w-20 rounded-full border-2 border-orange-300 shadow"
                    />
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ user.name }}</h2>
                        <p class="text-sm text-gray-600">{{ user.email }}</p>
                        <p class="text-sm text-gray-600">Member since: {{ formattedDate }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Personal Info -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="mb-3 text-lg font-semibold text-orange-700">Personal Information</h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li><span class="font-medium">Name:</span> {{ user.name }}</li>
                        <li><span class="font-medium">Email:</span> {{ user.email }}</li>
                        <li><span class="font-medium">Phone:</span> {{ user.phone ?? 'Not set' }}</li>
                    </ul>
                </div>

                <!-- Security -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="mb-3 text-lg font-semibold text-orange-700">Security</h3>
                    <p class="mb-3 text-sm text-gray-700">Keep your account secure by updating your password.</p>
                    <button
                        @click="openOtpModal"
                        class="w-full rounded-lg bg-orange-600 px-4 py-2 font-semibold text-white shadow hover:bg-orange-700"
                    >
                        Change Password
                    </button>
                </div>
            </div>
        </div>

        <!-- OTP Modal -->
        <BaseModal v-if="showOtpModal" @close="showOtpModal = false">
            <h2 class="mb-4 text-center text-xl font-bold text-orange-700">Change Password</h2>
            <form @submit.prevent="sendOtp">
                <p class="mb-4 text-sm text-gray-700">
                    An OTP will be sent to <span class="font-medium">{{ user.email }}</span
                    >.
                </p>

                <button
                    type="submit"
                    :disabled="sending"
                    class="w-full rounded-lg bg-orange-600 px-4 py-2 font-semibold text-white shadow hover:bg-orange-700 disabled:opacity-50"
                >
                    {{ sending ? 'Sending OTP...' : 'Send OTP' }}
                </button>
            </form>

            <!-- Feedback -->
            <p v-if="message" class="mt-4 text-center text-sm font-medium text-green-600">{{ message }}</p>
            <p v-if="error" class="mt-2 text-center text-sm font-medium text-red-600">{{ error }}</p>
        </BaseModal>
    </CustomerLayout>
</template>

<script>
import axios from 'axios';
import BaseModal from '../../components/BaseModal.vue';
import CustomerLayout from '../../layouts/CustomerLayout.vue';

export default {
    name: 'UserDetails',
    components: { CustomerLayout, BaseModal },
    data() {
        return {
            user: this.$page.props.auth.user, // from Inertia shared props
            showOtpModal: false,
            sending: false,
            message: '',
            error: '',
        };
    },
    computed: {
        formattedDate() {
            return new Date(this.user.created_at).toLocaleDateString();
        },
    },
    methods: {
        openOtpModal() {
            this.showOtpModal = true;
            this.message = '';
            this.error = '';
        },
        async sendOtp() {
            this.sending = true;
            this.message = '';
            this.error = '';
            try {
                const response = await axios.post('/password/send-otp', {
                    email: this.user.email,
                });

                this.message = response.data.message || 'OTP sent successfully!';
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to send OTP. Please try again.';
            } finally {
                this.sending = false;
            }
        },
    },
};
</script>
