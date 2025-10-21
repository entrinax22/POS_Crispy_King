<template>
    <div class="relative" ref="dropdown">
        <!-- Toggle Button -->
        <button @click="toggleDropdown" class="flex items-center gap-2 text-lg font-semibold text-gray-800 hover:text-orange-700">
            <span>{{ user.name }}</span>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div v-if="dropdownOpen" class="absolute right-0 z-50 mt-2 w-48 rounded-md border bg-white py-2 shadow-lg">
            <div class="border-b px-4 py-2 text-sm text-gray-700">
                {{ truncatedEmail }}
            </div>

            <button @click="profile" class="w-full px-4 py-2 text-left text-sm text-gray-600 hover:bg-yellow-600">My Profile</button>

            <button @click="history" class="w-full px-4 py-2 text-left text-sm text-gray-600 hover:bg-yellow-600">My Purchase History</button>

            <!-- ✅ Show Dashboard only if user has admin role -->
            <button v-if="isAdmin" @click="dashboard" class="w-full px-4 py-2 text-left text-sm text-gray-600 hover:bg-yellow-600">Dashboard</button>

            <button @click="logout" class="w-full px-4 py-2 text-left text-sm text-gray-600 hover:bg-yellow-600">Logout</button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'UserDropdown',
    props: {
        user: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            dropdownOpen: false,
        };
    },
    computed: {
        truncatedEmail() {
            return this.user.email.length > 24 ? this.user.email.slice(0, 21) + '...' : this.user.email;
        },

        isAdmin() {
            if (!this.user.roles) return false;

            if (Array.isArray(this.user.roles)) {
                return this.user.roles.includes('admin');
            }

            if (typeof this.user.roles === 'object') {
                return Object.values(this.user.roles).some((role) => role.name === 'admin');
            }

            return false;
        },
    },
    methods: {
        toggleDropdown() {
            this.dropdownOpen = !this.dropdownOpen;
        },
        closeDropdown() {
            this.dropdownOpen = false;
        },
        profile() {
            this.$inertia.get('/my-profile');
            this.closeDropdown();
        },
        history() {
            this.$inertia.get('/orders/customer/history');
            this.closeDropdown();
        },
        dashboard() {
            this.$inertia.get('/dashboard');
            this.closeDropdown();
        },
        logout() {
            this.$inertia.post('/logout');
            this.closeDropdown();
        },
        handleClickOutside(event) {
            if (this.$refs.dropdown && !this.$refs.dropdown.contains(event.target)) {
                this.closeDropdown();
            }
        },
        handleEscape(event) {
            if (event.key === 'Escape') {
                this.closeDropdown();
            }
        },
    },
    mounted() {
        document.addEventListener('click', this.handleClickOutside);
        document.addEventListener('keydown', this.handleEscape);
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
        document.removeEventListener('keydown', this.handleEscape);
    },
};
</script>
