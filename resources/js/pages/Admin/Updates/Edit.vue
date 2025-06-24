<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import UpdateForm from './Partials/UpdateForm.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

// Tipe Data untuk mendefinisikan struktur props
interface Program {
    id: number;
    name: string;
    slug: string;
}

interface Update {
    id: number;
    title: string;
    content: string;
    program: Program; // Relasi ke program induk
}

// Menerima data 'update' dari controller
const props = defineProps<{
    update: Update;
}>();

// Membuat breadcrumbs dinamis
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Kelola Kabar Terbaru', href: route('admin.news.index', { program: props.update.program.slug }) },
    { title: 'Edit Kabar', href: route('admin.news.edit', props.update.id) },
];
</script>

<template>

    <Head :title="`Edit Kabar: ${update.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <UpdateForm :program="update.program"
                :update="update" />
        </div>
    </AppLayout>
</template>
