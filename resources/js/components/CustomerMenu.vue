<template>
    <section id="menu" class="menu">
        <h2 class="mb-6 text-center text-2xl font-bold text-orange-700">Our Menu</h2>

        <!-- Loader -->
        <div v-if="loading" class="text-center text-gray-500">Loading menu...</div>

        <!-- Empty State -->
        <div v-else-if="!products.length" class="text-center text-gray-500">No menu items available.</div>

        <!-- Menu List -->
        <ul v-else class="menu-list grid gap-6 md:grid-cols-3">
            <li v-for="product in products" :key="product.product_id" class="flex flex-col items-center rounded-xl bg-white p-4 shadow-md">
                <img
                    :src="product.product_image"
                    :alt="product.product_name"
                    class="mb-3 h-24 w-24 rounded-full border-4 border-orange-100 object-cover shadow"
                />
                <h3 class="mb-1 text-lg font-bold text-orange-700">{{ product.product_name }}</h3>
                <p class="text-center text-gray-600">{{ product.product_description }}</p>
                <span class="mt-2 font-semibold text-orange-800">₱{{ product.product_price }}</span>
            </li>
        </ul>
    </section>
</template>

<script>
import axios from 'axios';

export default {
    name: 'CustomerMenu',
    data() {
        return {
            products: [],
            loading: true,
        };
    },
    async mounted() {
        try {
            const response = await axios.get('/products/user/menu');
            if (response.data.result) {
                this.products = response.data.data;
            }
        } catch (error) {
            console.error('Error fetching products:', error);
        } finally {
            this.loading = false;
        }
    },
};
</script>

<style scoped>
.menu {
    background: #fff;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}
.menu-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
@media (max-width: 600px) {
    .menu {
        padding: 1rem;
    }
    .menu-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
}
</style>
