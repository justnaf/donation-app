<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ProgramForm from './Partials/ProgramForm.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

interface Category { id: number; name: string; slug: string; }
const props = defineProps<{
    categories: Category[];
}>();
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Kelola Program Donasi', href: route('admin.programs.index') },
    { title: 'Buat Program Baru', href: route('admin.programs.create') },
];
</script>

<template>

    <Head title="Buat Program Donasi Baru" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="rounded-xl p-4">
            <ProgramForm :categories="props.categories" />
        </div>
    </AppLayout>
</template>