<template>
    <form @submit.prevent="submitReservation" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Table Number</label>
            <select
                v-model="form.table_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                required
            >
                <option value="" disabled selected>Select a table</option>
                <option v-for="table in tables" :key="table.table_id" :value="table.table_id">Table {{ table.table_number }}</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input
                v-model="form.name"
                type="text"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                placeholder="Your Name"
                required
            />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Contact Number</label>
            <input
                v-model="form.contact_number"
                type="tel"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                placeholder="e.g. 0917-123-4567"
                required
            />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Reservation Date & Time</label>
            <input
                v-model="form.reservation_time"
                type="datetime-local"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                required
            />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Number of Guests</label>
            <input
                v-model="form.number_guest"
                type="number"
                min="1"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                placeholder="e.g. 4"
                required
            />
        </div>

        <button
            type="submit"
            :disabled="loading"
            class="flex w-full items-center justify-center rounded-full bg-orange-600 px-4 py-2 text-lg font-semibold text-white shadow transition-colors duration-200 hover:bg-orange-700 disabled:cursor-not-allowed disabled:opacity-70"
            aria-busy="loading"
        >
            <LoaderCircle v-if="loading" :size="18" class="mr-2 text-white" />
            <span>{{ loading ? 'Reserving...' : 'Reserve Table' }}</span>
        </button>
    </form>
</template>
<script>
import LoaderCircle from '@/components/LoaderCircle.vue'; // adjust path if needed
import { useNotify } from '@/composables/useNotify';
import axios from 'axios';
import { onMounted, ref } from 'vue';

export default {
    name: 'CustomerTableReservationForm',
    components: { LoaderCircle },
    setup(props, { emit }) {
        const tables = ref([]);
        const notify = useNotify();
        const loading = ref(false); // <-- loader state

        const form = ref({
            table_id: '',
            name: '',
            contact_number: '',
            reservation_time: '',
            number_guest: 1,
        });

        const fetchTables = async () => {
            try {
                const routeUrl = route('tables.listOfTables');
                const res = await axios.get(routeUrl);
                if (res.data.result === true) {
                    tables.value = res.data.data;
                } else {
                    notify('Failed to fetch tables', 'error');
                }
            } catch (err) {
                notify(err.message || 'Error fetching tables', 'error');
            }
        };

        const submitReservation = async () => {
            loading.value = true;
            try {
                const response = await axios.post(route('tables.reserve'), form.value);
                if (response.data.result === true) {
                    notify(response.data.message, 'success');
                    form.value = {
                        table_id: '',
                        name: '',
                        contact_number: '',
                        reservation_time: '',
                        number_guest: 1,
                    };
                    emit('table-success');
                } else {
                    notify(response.data.message || 'Reservation failed', 'error');
                }
            } catch (error) {
                notify(error.response?.data?.message || 'Error submitting reservation', 'error');
            } finally {
                loading.value = false;
            }
        };

        onMounted(fetchTables);

        return {
            tables,
            form,
            submitReservation,
            loading, // return if you want to use it elsewhere
        };
    },
};
</script>
