<template>
    <CustomerLayout>
        <div class="mx-auto mt-10 w-full max-w-4xl rounded-2xl bg-white/90 p-8 shadow-xl ring-1 ring-orange-100 backdrop-blur">
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between border-b border-orange-100 pb-4">
                <h1 class="text-3xl font-extrabold text-orange-700">My Profile</h1>
                <button
                    @click="refreshProfile"
                    class="flex items-center gap-2 rounded-lg border border-orange-300 bg-white px-4 py-2 text-sm font-semibold text-orange-700 shadow-sm hover:bg-orange-50"
                >
                    <i class="fa-solid fa-rotate-right"></i>
                    Refresh
                </button>
            </div>

            <!-- Profile Section -->
            <div
                class="mb-10 flex flex-col items-center gap-5 rounded-2xl bg-gradient-to-br from-yellow-100 to-orange-50 p-8 shadow-inner md:flex-row md:items-start md:gap-8"
            >
                <img :src="userImage" alt="Profile" class="h-28 w-28 rounded-full border-4 border-orange-300 shadow-lg transition hover:scale-105" />
                <div class="text-center md:text-left">
                    <h2 class="text-2xl font-bold text-gray-800">{{ user.name }}</h2>
                    <p class="text-sm text-gray-600">{{ user.email }}</p>
                    <p class="mt-1 text-sm text-gray-600">
                        Member since <span class="font-medium text-orange-700">{{ formattedDate }}</span>
                    </p>
                    <span class="mt-3 inline-block rounded-full bg-orange-100 px-3 py-1 text-xs font-medium text-orange-700"> Verified Member </span>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Personal Info -->
                <div class="rounded-2xl border border-orange-100 bg-white p-6 shadow-md transition hover:shadow-lg">
                    <h3 class="mb-4 flex items-center gap-2 text-lg font-bold text-orange-700">
                        <i class="fa-solid fa-user text-orange-600"></i> Personal Information
                    </h3>
                    <ul class="space-y-3 text-sm text-gray-700">
                        <li><span class="font-semibold">Name:</span> {{ user.name }}</li>
                        <li><span class="font-semibold">Email:</span> {{ user.email }}</li>
                        <li><span class="font-semibold">Phone:</span> {{ user.phone ?? 'Not set' }}</li>
                        <li><span class="font-semibold">Role:</span> {{ user.role ?? 'Customer' }}</li>
                    </ul>
                </div>

                <!-- Security Info -->
                <div class="rounded-2xl border border-orange-100 bg-white p-6 shadow-md transition hover:shadow-lg">
                    <h3 class="mb-4 flex items-center gap-2 text-lg font-bold text-orange-700">
                        <i class="fa-solid fa-lock text-orange-600"></i> Security Settings
                    </h3>
                    <p class="mb-5 text-sm text-gray-600">Protect your account by changing your password regularly.</p>
                    <button
                        @click="openOtpModal"
                        class="w-full rounded-lg bg-orange-600 px-4 py-2 font-semibold text-white shadow-md transition hover:bg-orange-700"
                    >
                        Change Password
                    </button>
                </div>
            </div>
        </div>

        <!-- OTP Modal -->
        <BaseModal v-if="showOtpModal" @close="showOtpModal = false">
            <h2 class="mb-4 text-center text-xl font-bold text-orange-700">Request OTP</h2>
            <form @submit.prevent="sendOtp" class="space-y-4">
                <p class="text-center text-sm text-gray-700">
                    We will send a One-Time Password (OTP) to your registered email:
                    <span class="font-medium text-orange-700">{{ user.email }}</span
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

            <p v-if="message" class="mt-4 text-center text-sm font-semibold text-green-600">{{ message }}</p>
            <p v-if="error" class="mt-2 text-center text-sm font-semibold text-red-600">{{ error }}</p>

            <div v-if="message" class="mt-6 text-center">
                <button @click="proceedToVerify" class="rounded-lg bg-orange-500 px-4 py-2 font-semibold text-white transition hover:bg-orange-600">
                    Proceed to Verify OTP
                </button>
            </div>
        </BaseModal>

        <!-- OTP Verification Modal -->
        <BaseModal v-if="showVerifyModal" @close="showVerifyModal = false">
            <h2 class="mb-4 text-center text-xl font-bold text-orange-700">Verify OTP</h2>
            <form @submit.prevent="verifyOtp" class="space-y-4">
                <input
                    v-model="otp"
                    type="text"
                    maxlength="6"
                    placeholder="Enter 6-digit OTP"
                    class="w-full rounded-lg border border-orange-300 px-4 py-2 text-center text-lg tracking-widest focus:border-orange-500 focus:ring-orange-500"
                    required
                />

                <input
                    v-model="password"
                    type="password"
                    placeholder="New Password"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-orange-500 focus:ring-orange-500"
                    required
                />

                <input
                    v-model="password_confirmation"
                    type="password"
                    placeholder="Confirm Password"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-orange-500 focus:ring-orange-500"
                    required
                />

                <button
                    type="submit"
                    :disabled="verifying"
                    class="w-full rounded-lg bg-orange-600 px-4 py-2 font-semibold text-white shadow hover:bg-orange-700 disabled:opacity-50"
                >
                    {{ verifying ? 'Verifying...' : 'Verify & Update Password' }}
                </button>
            </form>

            <p v-if="verifyMessage" class="mt-4 text-center text-sm font-semibold text-green-600">
                {{ verifyMessage }}
            </p>
            <p v-if="verifyError" class="mt-2 text-center text-sm font-semibold text-red-600">
                {{ verifyError }}
            </p>
        </BaseModal>
    </CustomerLayout>
</template>

<script>
import axios from 'axios';
import BaseModal from '../../../components/BaseModal.vue';
import CustomerLayout from '../../../layouts/CustomerLayout.vue';

export default {
    name: 'UserDetails',
    components: { CustomerLayout, BaseModal },
    data() {
        return {
            user: this.$page.props.auth.user,
            showOtpModal: false,
            showVerifyModal: false,
            sending: false,
            verifying: false,
            otp: '',
            password: '',
            password_confirmation: '',
            message: '',
            error: '',
            verifyMessage: '',
            verifyError: '',
        };
    },
    computed: {
        formattedDate() {
            return new Date(this.user.created_at).toLocaleDateString();
        },
        userImage() {
            return this.user.image
                ? this.user.image
                : `https://ui-avatars.com/api/?name=${encodeURIComponent(this.user.name)}&background=ffb74d&color=fff`;
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
        proceedToVerify() {
            this.showOtpModal = false;
            this.showVerifyModal = true;
        },
        async verifyOtp() {
            this.verifying = true;
            this.verifyMessage = '';
            this.verifyError = '';
            try {
                const response = await axios.post('/password/verify-otp', {
                    email: this.user.email,
                    otp: this.otp,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                });
                this.verifyMessage = response.data.message || 'Password updated successfully!';
                this.otp = '';
                this.password = '';
                this.password_confirmation = '';
            } catch (err) {
                this.verifyError = err.response?.data?.message || 'Invalid OTP or password mismatch.';
            } finally {
                this.verifying = false;
            }
        },
        refreshProfile() {
            window.location.reload();
        },
    },
};
</script>

<style scoped>
button i {
    transition: transform 0.2s;
}
button:hover i {
    transform: rotate(30deg);
}
</style>
