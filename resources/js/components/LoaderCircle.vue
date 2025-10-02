<template>
    <!-- size is in pixels; color comes from currentColor so you can pass `class="text-white"` etc. -->
    <svg
        v-bind="$attrs"
        :style="{ width: size + 'px', height: size + 'px' }"
        class="animate-spin"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        role="status"
        aria-hidden="true"
    >
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
    </svg>
</template>

<script>
export default {
    name: 'LoaderCircle',
    props: {
        size: { type: Number, default: 18 }, // px
    },
};
</script>
