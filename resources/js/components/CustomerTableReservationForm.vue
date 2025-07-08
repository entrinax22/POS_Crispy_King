<template>
    <form class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input
                type="text"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                placeholder="Your Name"
                required
            />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Contact Number</label>
            <input
                type="tel"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                placeholder="e.g. 0917-123-4567"
                required
            />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Date</label>
            <input
                type="date"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                required
            />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Time</label>
            <input
                type="time"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                required
            />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Number of Guests</label>
            <input
                type="number"
                min="1"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                placeholder="e.g. 4"
                required
            />
        </div>
        <button
            type="submit"
            class="w-full rounded-full bg-orange-600 px-4 py-2 text-lg font-semibold text-white shadow transition-colors duration-200 hover:bg-orange-700"
        >
            Reserve Table
        </button>
    </form>
</template>

<script>
export default {
    name: 'CustomerTableReservationForm',
};
</script>
