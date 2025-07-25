<template>
    <Head title="Crispy King | Home" />
    <CustomerLayout />
</template>

<script>
import { Head } from '@inertiajs/vue3';
import CustomerLayout from '../../layouts/CustomerLayout.vue';

export default {
    name: 'CustomerIndexPage',
    components: {
        Head,
        CustomerLayout,
    },
};
</script>
