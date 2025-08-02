<template>
    <transition name="fade">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <button @click="$emit('close')" class="absolute top-4 right-4 text-2xl font-bold text-gray-400 hover:text-orange-600">&times;</button>
                <h2 class="mb-4 text-center text-2xl font-bold text-orange-700">Add Product</h2>
                <form @submit.prevent="addProduct">
                    <div class="mb-4">
                        <label class="mb-1 block font-medium text-gray-700">Product</label>
                        <select
                            v-model="selectedProductId"
                            required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-orange-400"
                        >
                            <option value="">Select a product</option>
                            <option v-for="product in products" :key="product.product_id" :value="product.product_id">
                                {{ product.product_name }} - ₱{{ product.product_price }}
                            </option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="mb-1 block font-medium text-gray-700">Quantity</label>
                        <input
                            v-model.number="quantity"
                            type="number"
                            min="1"
                            required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-orange-400"
                        />
                    </div>
                    <button
                        type="submit"
                        class="w-full rounded-full bg-orange-600 px-4 py-2 text-lg font-semibold text-white shadow transition-colors duration-200 hover:bg-orange-700"
                    >
                        Add to Order
                    </button>
                </form>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    name: 'ProductSelectorModal',
    props: {
        show: Boolean,
        products: Array,
    },
    data() {
        return {
            selectedProductId: '',
            quantity: 1,
        };
    },
    methods: {
        addProduct() {
            const product = this.products.find((p) => p.product_id === this.selectedProductId);
            if (product && this.quantity > 0) {
                this.$emit('add', {
                    product,
                    quantity: this.quantity,
                });
                this.selectedProductId = '';
                this.quantity = 1;
                this.$emit('close');
            }
        },
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
