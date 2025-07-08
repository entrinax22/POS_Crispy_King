<template>
    <section id="order" class="order">
        <form
            class="scrollbar-thin scrollbar-thumb-orange-200 scrollbar-track-orange-50 max-h-[60vh] space-y-6 overflow-y-auto md:max-h-none"
            @submit.prevent="submitOrder"
        >
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Full Name</label>
                    <input
                        v-model="customerName"
                        type="text"
                        placeholder="e.g. Juan Dela Cruz"
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-orange-400"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Contact Number</label>
                    <input
                        v-model="contactNumber"
                        type="tel"
                        placeholder="e.g. 0917-123-4567"
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-orange-400"
                    />
                </div>
            </div>
            <div class="space-y-4">
                <div v-if="items.length === 0" class="py-8 text-center text-gray-400">No products added yet.</div>
                <div
                    v-for="(item, idx) in items"
                    :key="idx"
                    class="relative flex flex-col gap-2 rounded-lg border border-orange-100 bg-orange-50 p-4 md:flex-row md:items-end md:gap-4"
                >
                    <div class="flex-1">
                        <div class="font-semibold text-gray-800">{{ item.product }}</div>
                        <div class="text-sm text-gray-500">Qty: {{ item.quantity }}</div>
                    </div>
                    <div class="w-full md:w-28">
                        <div class="text-sm text-gray-700">Price</div>
                        <div class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-center text-gray-700">
                            ₱{{ getProductPrice(item.product) }}
                        </div>
                    </div>
                    <div class="w-full md:w-32">
                        <div class="text-sm text-gray-700">Subtotal</div>
                        <div class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-center text-gray-700">₱{{ getSubtotal(item) }}</div>
                    </div>
                    <button
                        v-if="items.length > 0"
                        type="button"
                        @click="removeItem(idx)"
                        class="absolute top-2 right-2 flex h-8 w-8 items-center justify-center rounded-full border border-gray-200 bg-white text-xl font-bold text-red-400 shadow-sm hover:text-red-600"
                    >
                        &times;
                    </button>
                </div>
                <button
                    type="button"
                    @click="showProductModal = true"
                    class="flex w-full items-center justify-center gap-1 rounded-full border border-orange-400 bg-orange-100 px-4 py-2 font-semibold text-orange-700 transition hover:bg-orange-200 md:w-auto md:justify-start"
                >
                    <span class="text-xl leading-none">+</span> Add Product
                </button>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-1">
                <div class="flex flex-col items-end justify-end md:items-end">
                    <div class="mt-2 flex w-full items-center justify-between border-t pt-4">
                        <span class="text-lg font-semibold text-gray-700">Total Amount</span>
                        <span class="text-2xl font-bold text-orange-700">₱{{ totalAmount }}</span>
                    </div>
                </div>
            </div>
            <button
                type="submit"
                class="mt-2 w-full rounded-full bg-orange-600 px-4 py-3 text-lg font-semibold text-white shadow transition-colors duration-200 hover:bg-orange-700"
            >
                Place Order
            </button>
        </form>
        <ProductSelectorModal :show="showProductModal" :products="products" @close="showProductModal = false" @add="addProductFromModal" />
    </section>
</template>

<script>
import ProductSelectorModal from './ProductSelectorModal.vue';
export default {
    name: 'CustomerOrderForm',
    components: { ProductSelectorModal },
    data() {
        return {
            customerName: '',
            contactNumber: '',
            orderNotes: '',
            products: [
                { name: 'Signature Fried Chicken', price: 120 },
                { name: 'Chicken Burger', price: 95 },
                { name: 'Family Meal', price: 450 },
            ],
            items: [],
            showProductModal: false,
        };
    },
    computed: {
        totalAmount() {
            return this.items.reduce((sum, item) => {
                const price = this.getProductPrice(item.product);
                return sum + price * (item.quantity || 0);
            }, 0);
        },
    },
    methods: {
        addProductFromModal({ product, quantity }) {
            this.items.push({ product, quantity });
        },
        removeItem(idx) {
            this.items.splice(idx, 1);
        },
        getProductPrice(productName) {
            const found = this.products.find((p) => p.name === productName);
            return found ? found.price : 0;
        },
        getSubtotal(item) {
            return this.getProductPrice(item.product) * (item.quantity || 0);
        },
        submitOrder() {
            const order = {
                name: this.customerName,
                contact: this.contactNumber,
                notes: this.orderNotes,
                items: this.items
                    .filter((i) => i.product && i.quantity > 0)
                    .map((i) => ({
                        product: i.product,
                        quantity: i.quantity,
                        price: this.getProductPrice(i.product),
                        subtotal: this.getSubtotal(i),
                    })),
                total: this.totalAmount,
            };
            // eslint-disable-next-line no-alert
            alert('Order submitted!\n' + JSON.stringify(order, null, 2));
            // Reset form
            this.customerName = '';
            this.contactNumber = '';
            this.orderNotes = '';
            this.items = [];
        },
    },
};
</script>
