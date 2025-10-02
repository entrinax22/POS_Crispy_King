<template>
    <section id="order" class="order">
        <form class="space-y-2" @submit.prevent="submitOrder">
            <!-- Address / Phone / Order Type -->
            <div class="grid grid-cols-1 gap-2">
                <div>
                    <label class="mb-0.5 block text-xs font-medium text-gray-600">Address</label>
                    <input
                        v-model="address"
                        type="text"
                        placeholder="e.g. Purok 5, Bgry. San Juan"
                        required
                        class="w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-orange-400"
                    />
                </div>
                <div>
                    <label class="mb-0.5 block text-xs font-medium text-gray-600">Contact Number</label>
                    <input
                        v-model="phone_number"
                        type="tel"
                        placeholder="e.g. 0917-123-4567"
                        required
                        class="w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-orange-400"
                    />
                </div>
                <div>
                    <label class="mb-0.5 block text-xs font-medium text-gray-600">Order Type</label>
                    <select
                        v-model="orderType"
                        class="w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-orange-400"
                        required
                    >
                        <option value="" disabled>Select order type</option>
                        <option value="dine_in">Dine In</option>
                        <option value="delivery">Delivery</option>
                    </select>
                </div>
                <div v-if="orderType === 'delivery'">
                    <label class="mb-0.5 block text-xs font-medium text-gray-600">Delivery Time</label>
                    <input
                        v-model="delivery_time"
                        type="datetime-local"
                        required
                        class="w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-orange-400"
                    />
                </div>
            </div>

            <!-- Order Items -->
            <div class="scrollbar-thin scrollbar-thumb-orange-200 scrollbar-track-orange-50 max-h-[30vh] space-y-2 overflow-y-auto px-1">
                <div v-if="items.length === 0" class="py-4 text-center text-sm text-gray-400">No products added yet.</div>

                <div
                    v-for="(item, idx) in items"
                    :key="idx"
                    class="relative flex flex-col gap-2 rounded-lg border border-orange-200 bg-white p-2 shadow-sm md:flex-row md:items-center md:gap-3"
                >
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-orange-700">{{ item.product.product_name }}</div>
                        <div class="text-xs text-gray-500">
                            Qty: <span class="font-semibold">{{ item.quantity }}</span>
                        </div>
                    </div>
                    <div class="w-full md:w-24">
                        <div class="text-[11px] text-gray-500">Price</div>
                        <div class="rounded border border-gray-200 bg-orange-50 px-2 py-1 text-center text-sm font-semibold text-orange-700">
                            ₱{{ getProductPrice(item.product) }}
                        </div>
                    </div>
                    <div class="w-full md:w-28">
                        <div class="text-[11px] text-gray-500">Subtotal</div>
                        <div class="rounded border border-gray-200 bg-orange-50 px-2 py-1 text-center text-sm font-semibold text-orange-700">
                            ₱{{ getSubtotal(item) }}
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="removeItem(idx)"
                        class="absolute top-1 right-1 flex h-6 w-6 items-center justify-center rounded-full border border-gray-200 bg-white text-sm font-bold text-red-400 hover:text-red-600"
                    >
                        &times;
                    </button>
                </div>

                <button
                    type="button"
                    @click="showProductModal = true"
                    class="flex w-full items-center justify-center gap-1 rounded border border-orange-400 bg-orange-100 px-3 py-2 text-sm font-medium text-orange-700 hover:bg-orange-200 md:w-auto"
                >
                    <span class="text-lg leading-none">+</span> Add Product
                </button>
            </div>

            <!-- Sticky Total -->
            <div class="sticky bottom-0 border-t bg-white pt-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Total</span>
                    <span class="text-lg font-bold text-orange-700">₱{{ totalAmount }}</span>
                </div>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                :disabled="loading"
                class="mt-2 flex w-full items-center justify-center rounded bg-orange-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-orange-700 disabled:cursor-not-allowed disabled:opacity-70"
            >
                <LoaderCircle v-if="loading" :size="16" class="mr-1 text-white" />
                <span>{{ loading ? 'Placing Order...' : 'Place Order' }}</span>
            </button>
        </form>

        <!-- Product Selector Modal -->
        <ProductSelectorModal :show="showProductModal" :products="products" @close="showProductModal = false" @add="addProductFromModal" />
    </section>
</template>

<script>
import axios from 'axios';
import LoaderCircle from './LoaderCircle.vue';
import ProductSelectorModal from './ProductSelectorModal.vue';

export default {
    name: 'CustomerOrderForm',
    components: { ProductSelectorModal, LoaderCircle },
    data() {
        return {
            address: '',
            phone_number: '',
            orderType: 'dine_in',
            delivery_time: '',
            products: [],
            items: [],
            showProductModal: false,
            loading: false,
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
            const existingItem = this.items.find((i) => i.product.product_id === product.product_id);
            if (existingItem) {
                existingItem.quantity += quantity; // 🔹 merge instead of duplicate
            } else {
                this.items.push({ product, quantity });
            }
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
        async submitOrder() {
            if (this.items.length === 0) return;

            this.loading = true;
            try {
                await axios.post(route('orders.orderOnline'), {
                    cart: this.items.map((item) => ({
                        product_id: item.product.product_id,
                        product_price: parseFloat(item.product.product_price),
                        product_quantity: item.quantity,
                    })),
                    address: this.address,
                    phone_number: this.phone_number,
                    total_amount: this.totalAmount,
                    orderType: this.orderType,
                    delivery_time: this.delivery_time,
                    status: 'pending',
                });

                // Reset form
                this.address = '';
                this.phone_number = '';
                this.items = [];
                this.orderType = 'dine_in';
                this.delivery_time = '';
                this.$emit('order-success');
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
