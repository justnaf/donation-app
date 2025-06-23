<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType, FlashProps } from '@/types';
import useSweetAlert from '@/composables/useSweetAlert';
import { watchEffect } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

const page = usePage();
withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
const { success, error } = useSweetAlert();

watchEffect(() => {
    console.log(page.props);
    const flash = page.props as FlashProps;
    if (flash?.flash?.success) {
        success(flash.flash.success);
        page.props.success = null;
    }
    if (flash?.flash?.error) {
        error(flash.flash.error);
        page.props.error = null;
    }
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
