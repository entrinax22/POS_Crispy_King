<template>
    <div class="flex min-h-screen flex-col bg-gradient-to-br from-yellow-50 to-orange-100">
        <!-- Header -->
        <header class="sticky top-0 z-30 flex items-center justify-between rounded-b-2xl bg-yellow-400/90 px-4 py-4 shadow-lg md:px-8">
            <div class="flex items-center gap-3">
                <img src="/CrispyKing.png" alt="Crispy King Logo" class="h-10 w-10 rounded-full border-2 border-white shadow-md md:h-12 md:w-12" />
                <span class="text-xl font-extrabold tracking-tight text-orange-800 drop-shadow md:text-2xl"> Crispy King </span>
            </div>

            <!-- Desktop Nav -->
            <nav class="hidden items-center space-x-8 md:flex">
                <a href="/" class="text-lg font-semibold hover:text-orange-700">Home</a>
                <UserDropdown :user="user" @logout="logout" />
                <button
                    @click="showReservation = true"
                    class="rounded-full border border-orange-600 bg-white px-4 py-2 text-lg font-semibold text-orange-700 shadow hover:bg-orange-100"
                >
                    Reserve Table
                </button>
            </nav>

            <!-- Mobile Nav Toggle -->
            <button @click="showMobileNav = !showMobileNav" class="rounded p-2 focus:ring-2 focus:ring-orange-400 focus:outline-none md:hidden">
                <svg class="h-7 w-7 text-orange-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </header>

        <!-- Mobile Nav -->
        <transition name="fade">
            <nav
                v-if="showMobileNav"
                class="absolute top-16 left-0 z-40 flex w-full flex-col items-end space-y-4 bg-yellow-400/95 px-4 py-4 shadow-lg md:hidden"
            >
                <a @click="showMobileNav = false" href="/" class="text-lg font-semibold hover:text-orange-700">Home</a>
                <a @click="showMobileNav = false" href="#menu" class="text-lg font-semibold hover:text-orange-700">Menu</a>
                <UserDropdown :user="user" @logout="logout" />
                <button
                    @click="
                        showReservation = true;
                        showMobileNav = false;
                    "
                    class="rounded-full border border-orange-600 bg-white px-4 py-2 text-lg font-semibold text-orange-700 shadow hover:bg-orange-100"
                >
                    Reserve Table
                </button>
            </nav>
        </transition>

        <!-- Main content -->
        <main class="mx-auto mt-4 mb-8 w-full max-w-4xl flex-1 px-2 md:px-0">
            <slot />
        </main>

        <!-- Reservation Modal -->
        <transition name="fade">
            <div v-if="showReservation" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
                <div class="relative w-[90%] max-w-md rounded-2xl bg-white p-6 shadow-xl">
                    <button @click="showReservation = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">✕</button>
                    <h2 class="mb-4 text-center text-xl font-semibold text-orange-700">Reserve a Table</h2>

                    <CustomerTableReservationForm @table-success="onReservationSuccess" />
                </div>
            </div>
        </transition>

        <!-- Footer -->
        <footer class="mt-auto rounded-t-2xl bg-orange-600 py-6 text-center text-sm tracking-wide text-gray-200 shadow-inner">
            <div class="flex flex-col items-center gap-2">
                <span>&copy; 2025 Crispy King. All rights reserved.</span>
                <span class="text-xs"> Designed with <span class="text-red-400">♥</span> for food lovers </span>
            </div>
        </footer>
    </div>
</template>

<script>
import CustomerTableReservationForm from '../components/CustomerTableReservationForm.vue';
import UserDropdown from '../components/UserDropdown.vue';

export default {
    name: 'CustomerLayout',
    components: { UserDropdown, CustomerTableReservationForm },
    data() {
        return {
            showMobileNav: false,
            showReservation: false,
            user: this.$page.props.auth.user,
        };
    },
    methods: {
        logout() {
            this.$inertia.post('/logout');
        },
        onReservationSuccess() {
            this.showReservation = false;
        },
    },
};
</script>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>
