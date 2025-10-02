<template>
    <CustomerLayout>
        <!-- Page Content -->
        <CustomerHero @order-now="showOrderModal = true" />
        <CustomerAbout />
        <CustomerMenu />

        <!-- Modals -->
        <BaseModal v-if="showOrderModal" @close="showOrderModal = false">
            <h2 class="mb-2 text-center text-2xl font-bold text-orange-700">Order Online</h2>
            <CustomerOrderForm @order-success="handleOrderSuccess" />
        </BaseModal>

        <BaseModal v-if="showReservationModal" @close="showReservationModal = false">
            <h2 class="mb-2 text-center text-2xl font-bold text-orange-700">Table Reservation</h2>
            <CustomerTableReservationForm @table-success="handleTableSuccess" />
        </BaseModal>

        <SuccessModal :show="showSuccessModal" message="Order placed successfully!" @close="showSuccessModal = false" />
        <CustomerTableSuccessModal :show="showTableSuccessModal" @close="showTableSuccessModal = false" />
    </CustomerLayout>
</template>

<script>
import BaseModal from '../../components/BaseModal.vue';
import CustomerAbout from '../../components/CustomerAbout.vue';
import CustomerHero from '../../components/CustomerHero.vue';
import CustomerMenu from '../../components/CustomerMenu.vue';
import CustomerOrderForm from '../../components/CustomerOrderForm.vue';
import SuccessModal from '../../components/CustomerSuccessModal.vue';
import CustomerTableReservationForm from '../../components/CustomerTableReservationForm.vue';
import CustomerTableSuccessModal from '../../components/CustomerTableSuccessModal.vue';
import CustomerLayout from '../../layouts/CustomerLayout.vue';

export default {
    components: {
        CustomerLayout,
        CustomerHero,
        CustomerAbout,
        CustomerMenu,
        CustomerOrderForm,
        CustomerTableReservationForm,
        SuccessModal,
        CustomerTableSuccessModal,
        BaseModal,
    },
    data() {
        return {
            showOrderModal: false,
            showReservationModal: false,
            showSuccessModal: false,
            showTableSuccessModal: false,
        };
    },
    methods: {
        handleOrderSuccess() {
            this.showOrderModal = false;
            this.showSuccessModal = true;
        },
        handleTableSuccess() {
            this.showReservationModal = false;
            this.showTableSuccessModal = true;
        },
    },
};
</script>
