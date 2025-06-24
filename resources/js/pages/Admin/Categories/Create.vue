<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import CategoryForm from './Partials/CategoryForm.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Kelola Kategori', href: route('admin.donation-categories.index') },
    { title: 'Buat Kategori Baru', href: route('admin.donation-categories.create') },
];
</script>

<template>

    <Head title="Buat Kategori Baru" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="rounded-xl p-4">
            <CategoryForm />
        </div>
    </AppLayout>
</template>