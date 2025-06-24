<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

defineProps<{
    links: PaginationLink[];
}>();
</script>

<template>
    <div v-if="links.length > 3" class="mt-6">
        <div class="-mb-1 flex flex-wrap">
            <template v-for="(link, key) in links"
                :key="key">
                <div v-if="!link.url"
                    class="mr-1 mb-1 rounded border px-4 py-3 text-sm text-gray-400 dark:border-gray-700"
                    v-html="link.label" />
                <Link v-else
                    class="mr-1 mb-1 rounded border px-4 py-3 text-sm transition-colors duration-150 hover:bg-gray-100 focus:border-grey-500 focus:text-grey-500 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700"
                    :class="{ 'bg-black text-white font-bold dark:bg-gray-600': link.active }"
                    :href="link.url">
                <span v-html="link.label" />
                </Link>
            </template>
        </div>
    </div>
</template>