<template>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-left text-sm text-gray-500 rtl:text-right dark:text-gray-400">
            <thead class="bg-gray-300 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th v-for="col in columns" :key="col.key" class="px-6 py-3">
                        {{ col.label }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(row, index) in rows"
                    :key="row[uniqueKey]"
                    class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600"
                >
                    <td v-for="col in columns" :key="col.key" class="px-6 py-4 font-medium whitespace-nowrap text-gray-900 dark:text-white">
                        <slot :name="col.key" :row="row" :index="index">
                            {{ row[col.key] }}
                        </slot>
                    </td>
                </tr>
            </tbody>
            <tfoot v-if="pagination">
                <tr>
                    <td colspan="1" class="px-6 py-4">
                        <span class="text-sm text-gray-600"> Total: {{ safePagination.total }} products </span>
                    </td>
                    <td :colspan="columns.length - 1" class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button
                                :disabled="safePagination.current_page === 1"
                                @click="$emit('page-change', safePagination.current_page - 1)"
                                class="rounded border bg-gray-200 px-2 py-1 hover:bg-gray-300 disabled:opacity-50"
                                aria-label="Previous page"
                            >
                                &laquo;
                            </button>
                            <template v-for="page in Math.min(safePagination.last_page, 5)" :key="page">
                                <button
                                    v-if="page === 1 || page === safePagination.last_page || Math.abs(page - safePagination.current_page) <= 1"
                                    :class="[
                                        'rounded border px-3 py-1',
                                        page === safePagination.current_page ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300',
                                    ]"
                                    @click="$emit('page-change', page)"
                                    :disabled="page === safePagination.current_page"
                                >
                                    {{ page }}
                                </button>
                                <span v-else-if="page === safePagination.current_page - 2 || page === safePagination.current_page + 2" class="px-2"
                                    >...</span
                                >
                            </template>
                            <button
                                :disabled="safePagination.current_page === safePagination.last_page"
                                @click="$emit('page-change', safePagination.current_page + 1)"
                                class="rounded border bg-gray-200 px-2 py-1 hover:bg-gray-300 disabled:opacity-50"
                                aria-label="Next page"
                            >
                                &raquo;
                            </button>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>

<script setup lang="ts">
import { computed, defineProps } from 'vue';

const props = defineProps<{
    columns: Array<{ label: string; key: string }>;
    rows: Array<any>;
    pagination?: { total?: number; current_page?: number; last_page?: number; per_page?: number };
    uniqueKey: string;
}>();

const uniqueKey = props.uniqueKey ?? 'id';

// Defensive computed pagination values
const safePagination = computed(() => {
    const p = props.pagination || {};
    return {
        total: Number.isFinite(p.total) ? p.total : 0,
        current_page: Number.isFinite(p.current_page) && p.current_page > 0 ? p.current_page : 1,
        last_page: Number.isFinite(p.last_page) && p.last_page > 0 ? p.last_page : 1,
        per_page: Number.isFinite(p.per_page) && p.per_page > 0 ? p.per_page : 10,
    };
});
</script>
