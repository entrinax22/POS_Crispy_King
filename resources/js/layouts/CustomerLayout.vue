<template>
    <div class="flex min-h-screen flex-col bg-gradient-to-br from-yellow-50 to-orange-100">
        <header class="sticky top-0 z-30 flex items-center justify-between rounded-b-2xl bg-yellow-400/90 px-4 py-4 shadow-lg md:px-8">
            <div class="flex items-center gap-3">
                <img src="/CrispyKing.png" alt="Crispy King Logo" class="h-10 w-10 rounded-full border-2 border-white shadow-md md:h-12 md:w-12" />
                <span class="text-xl font-extrabold tracking-tight text-orange-800 drop-shadow md:text-2xl">Crispy King</span>
            </div>
            <button @click="showMobileNav = !showMobileNav" class="rounded p-2 focus:ring-2 focus:ring-orange-400 focus:outline-none md:hidden">
                <svg class="h-7 w-7 text-orange-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <nav class="hidden space-x-8 md:flex">
                <a href="#about" class="text-lg font-semibold text-gray-800 transition-colors duration-200 hover:text-orange-700">About</a>
                <a href="#menu" class="text-lg font-semibold text-gray-800 transition-colors duration-200 hover:text-orange-700">Menu</a>
                <a
                    @click.prevent="showOrderModal = true"
                    href="#order"
                    class="rounded-full bg-orange-600 px-4 py-2 text-lg font-semibold text-white shadow transition-colors duration-200 hover:bg-orange-700"
                    >Order Now</a
                >
                <a
                    @click.prevent="showReservationModal = true"
                    href="#reservation"
                    class="rounded-full border border-orange-600 bg-white px-4 py-2 text-lg font-semibold text-orange-700 shadow transition-colors duration-200 hover:bg-orange-100"
                    >Reserve Table</a
                >
            </nav>
            <transition name="fade">
                <nav
                    v-if="showMobileNav"
                    class="absolute top-full left-0 z-40 flex w-full flex-col items-center space-y-2 bg-yellow-400/95 py-4 shadow-lg md:hidden"
                >
                    <a @click="showMobileNav = false" href="#about" class="text-lg font-semibold text-gray-800 hover:text-orange-700">About</a>
                    <a @click="showMobileNav = false" href="#menu" class="text-lg font-semibold text-gray-800 hover:text-orange-700">Menu</a>
                    <a
                        @click.prevent="
                            showOrderModal = true;
                            showMobileNav = false;
                        "
                        href="#order"
                        class="rounded-full bg-orange-600 px-4 py-2 text-lg font-semibold text-white shadow hover:bg-orange-700"
                        >Order Now</a
                    >
                    <a
                        @click.prevent="
                            showReservationModal = true;
                            showMobileNav = false;
                        "
                        href="#reservation"
                        class="rounded-full border border-orange-600 bg-white px-4 py-2 text-lg font-semibold text-orange-700 shadow hover:bg-orange-100"
                        >Reserve Table</a
                    >
                </nav>
            </transition>
        </header>
        <main class="mx-auto mt-4 mb-8 w-full max-w-4xl flex-1 px-2 md:px-0">
            <CustomerHero @order-now="showOrderModal = true" />
            <CustomerAbout />
            <CustomerMenu />
            <!-- Order Modal -->
            <transition name="fade">
                <div v-if="showOrderModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
                    <div class="relative w-full max-w-lg rounded-2xl bg-white p-8 shadow-2xl">
                        <button @click="showOrderModal = false" class="absolute top-4 right-4 text-2xl font-bold text-gray-400 hover:text-orange-600">
                            &times;
                        </button>
                        <h2 class="mb-2 text-center text-2xl font-bold text-orange-700">Order Online</h2>
                        <p class="mb-6 text-center text-gray-500">Fill out the form below to place your order. We'll get it ready for you!</p>
                        <CustomerOrderForm />
                    </div>
                </div>
            </transition>
            <!-- Table Reservation Modal -->
            <transition name="fade">
                <div v-if="showReservationModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
                    <div class="relative w-full max-w-lg rounded-2xl bg-white p-8 shadow-2xl">
                        <button
                            @click="showReservationModal = false"
                            class="absolute top-4 right-4 text-2xl font-bold text-gray-400 hover:text-orange-600"
                        >
                            &times;
                        </button>
                        <h2 class="mb-2 text-center text-2xl font-bold text-orange-700">Table Reservation</h2>
                        <p class="mb-6 text-center text-gray-500">Reserve your table in advance for a royal experience!</p>
                        <CustomerTableReservationForm />
                    </div>
                </div>
            </transition>
        </main>
        <footer class="mt-auto rounded-t-2xl bg-orange-600 py-6 text-center text-sm tracking-wide text-gray-200 shadow-inner">
            <div class="flex flex-col items-center gap-2">
                <span>&copy; 2025 Crispy King. All rights reserved.</span>
                <span class="text-xs">Designed with <span class="text-red-400">♥</span> for food lovers</span>
            </div>
        </footer>
    </div>
</template>

<script>
import CustomerAbout from '../components/CustomerAbout.vue';
import CustomerHero from '../components/CustomerHero.vue';
import CustomerMenu from '../components/CustomerMenu.vue';
import CustomerOrderForm from '../components/CustomerOrderForm.vue';
import CustomerTableReservationForm from '../components/CustomerTableReservationForm.vue';

export default {
    name: 'CustomerLayout',
    components: {
        CustomerHero,
        CustomerAbout,
        CustomerMenu,
        CustomerOrderForm,
        CustomerTableReservationForm,
    },
    data() {
        return {
            showOrderModal: false,
            showReservationModal: false,
            showMobileNav: false,
        };
    },
};
</script>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
