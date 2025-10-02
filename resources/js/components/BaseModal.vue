<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" @click.self="$emit('close')">
        <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-lg">
            <!-- Close Button -->
            <button @click="$emit('close')" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">✕</button>

            <!-- Slot for Modal Content -->
            <div>
                <slot />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'BaseModal',
};
</script>
