<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import CategoryForm from './Partials/CategoryForm.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

interface Category {
    id: number;
    slug: string;
    name: string;
    description: string | null;
}

const props = defineProps<{
    category: Category;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Kelola Kategori', href: route('admin.donation-categories.index') },
    { title: props.category.name, href: route('admin.donation-categories.edit', props.category.slug) },
    { title: 'Edit', href: route('admin.donation-categories.edit', props.category.slug) },
];
</script>

<template>

    <Head :title="`Edit Kategori: ${category.name}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="rounded-xl p-4">
            <CategoryForm :category="category" />
        </div>
    </AppLayout>
</template>