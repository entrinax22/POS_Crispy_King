<template>
    <transition name="fade">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="relative w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">
                <button @click="handleClose" class="absolute top-4 right-4 text-2xl font-bold text-gray-400 hover:text-orange-600">&times;</button>
                <div class="text-center">
                    <h2 class="mb-2 text-2xl font-bold text-green-600">Table Reserved</h2>
                    <p class="mb-4 text-gray-600">Thank you! Table reserved successfully..</p>
                    <button @click="handleClose" class="mt-4 rounded-md bg-orange-600 px-6 py-2 text-white hover:bg-orange-700">Close</button>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
const props = defineProps({
    show: Boolean,
    message: {
        type: String,
        default: 'Table reserved successfully.',
    },
});
const emit = defineEmits(['close']);

const handleClose = () => emit('close');
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
