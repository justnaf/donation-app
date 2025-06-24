<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import UpdateForm from './Partials/UpdateForm.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

interface Program {
    id: number;
    name: string;
    slug: string;
}

// Menerima data program induk dari controller
const props = defineProps<{
    program: Program;
}>();

console.log(props);


// Membuat breadcrumbs dinamis yang menyertakan nama program
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Kelola Program', href: route('admin.programs.index') },
    { title: props.program.name, href: route('admin.programs.edit', { program: props.program.slug }) },
    { title: 'Tambah Kabar Baru', href: route('admin.news.create', props.program.slug) },
];
</script>

<template>

    <Head title="Tambah Kabar Baru" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6">
            <UpdateForm :program="program" />
        </div>
    </AppLayout>
</template>
