import { onMounted, ref } from 'vue';

export function useAppearance() {
    const appearance = ref<'light'>('light');

    onMounted(() => {
        // Always force light theme
        document.documentElement.classList.remove('dark');
    });

    function updateAppearance() {
        // Do nothing – light mode only
    }

    return {
        appearance,
        updateAppearance,
    };
}

export function initializeTheme() {
    if (typeof window !== 'undefined') {
        document.documentElement.classList.remove('dark');
    }
}
