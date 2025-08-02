<template>
    <section id="order" class="order">
        <form
            class="scrollbar-thin scrollbar-thumb-orange-200 scrollbar-track-orange-50 max-h-[60vh] space-y-6 overflow-y-auto md:max-h-none"
            @submit.prevent="submitOrder"
        >
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Address</label>
                    <input
                        v-model="address"
                        type="text"
                        placeholder="e.g. Purok 5, Bgry. San Juan"
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-orange-400"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Contact Number</label>
                    <input
                        v-model="phone_number"
                        type="tel"
                        placeholder="e.g. 0917-123-4567"
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-orange-400"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Order Type</label>
                    <select
                        v-model="orderType"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-orange-400"
                        required
                    >
                        <option value="" disabled>Select order type</option>
                        <option value="dine_in">Dine In</option>
                        <option value="delivery">Delivery</option>
                    </select>
                </div>
                <div v-if="orderType === 'delivery'">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Delivery Time</label>
                    <input
                        v-model="delivery_time"
                        type="datetime-local"
                        placeholder="Select delivery time"
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-orange-400"
                    />
                </div>
            </div>
            <div
                class="scrollbar-thin scrollbar-thumb-orange-200 scrollbar-track-orange-50 max-h-[30vh] space-y-4 overflow-y-auto px-2 md:max-h-[40vh]"
            >
                <div v-if="items.length === 0" class="py-8 text-center text-base text-gray-400">No products added yet.</div>
                <div
                    v-for="(item, idx) in items"
                    :key="idx"
                    class="relative flex flex-col gap-3 rounded-xl border border-orange-200 bg-white p-4 shadow-sm md:flex-row md:items-end md:gap-4"
                >
                    <div class="flex-1">
                        <div class="text-lg font-bold text-orange-700">{{ item.product.product_name }}</div>
                        <div class="text-sm text-gray-500">
                            Qty: <span class="font-semibold text-gray-700">{{ item.quantity }}</span>
                        </div>
                    </div>
                    <div class="w-full md:w-28">
                        <div class="text-xs text-gray-500">Price</div>
                        <div class="rounded-lg border border-gray-200 bg-orange-50 px-3 py-2 text-center font-semibold text-orange-700">
                            ₱{{ getProductPrice(item.product) }}
                        </div>
                    </div>
                    <div class="w-full md:w-32">
                        <div class="text-xs text-gray-500">Subtotal</div>
                        <div class="rounded-lg border border-gray-200 bg-orange-50 px-3 py-2 text-center font-semibold text-orange-700">
                            ₱{{ getSubtotal(item) }}
                        </div>
                    </div>
                    <button
                        v-if="items.length > 0"
                        type="button"
                        @click="removeItem(idx)"
                        class="absolute top-2 right-2 flex h-8 w-8 items-center justify-center rounded-full border border-gray-200 bg-white text-xl font-bold text-red-400 shadow hover:text-red-600 focus:outline-none"
                        aria-label="Remove item"
                    >
                        &times;
                    </button>
                </div>
                <button
                    type="button"
                    @click="showProductModal = true"
                    class="flex w-full items-center justify-center gap-2 rounded-full border border-orange-400 bg-orange-100 px-4 py-3 text-base font-bold text-orange-700 transition hover:bg-orange-200 md:w-auto md:justify-start"
                >
                    <span class="text-2xl leading-none">+</span> Add Product
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
import axios from 'axios';
import ProductSelectorModal from './ProductSelectorModal.vue';

export default {
    name: 'CustomerOrderForm',
    components: { ProductSelectorModal },
    data() {
        return {
            address: '',
            phone_number: '',
            orderType: 'dine_in',
            delivery_time: '',
            products: [],
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
    mounted() {
        this.fetchProductData();
    },
    methods: {
        fetchProductData() {
            axios
                .get(route('products.list'))
                .then((response) => {
                    if (response.data.result === true) {
                        this.products = response.data.data;
                    }
                })
                .catch((error) => {
                    console.error('Error fetching products:', error);
                });
        },
        addProductFromModal({ product, quantity }) {
            this.items.push({ product, quantity });
        },
        removeItem(idx) {
            this.items.splice(idx, 1);
        },
        getProductPrice(product) {
            return parseFloat(product?.product_price || 0);
        },
        getSubtotal(item) {
            return this.getProductPrice(item.product) * (item.quantity || 0);
        },
        submitOrder() {
            axios
                .post(route('orders.orderOnline'), {
                    cart: this.items.map((item) => ({
                        product_id: item.product.product_id,
                        product_price: parseFloat(item.product.product_price),
                        product_quantity: item.quantity,
                    })),
                    address: this.address,
                    phone_number: this.phone_number,
                    total_amount: this.totalAmount,
                    orderType: 'dine_in',
                    delivery_time: this.delivery_time,
                    status: 'pending',
                })
                .then((response) => {
                    this.address = '';
                    this.phone_number = '';
                    this.orderNotes = '';
                    this.items = [];
                    this.$emit('order-success');
                })
                .catch((error) => {
                    console.error(error);
                });
        },
    },
};
</script>
