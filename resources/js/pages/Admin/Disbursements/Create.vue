<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DisbursementForm from './Partials/DisbursementForm.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

// Tipe Data untuk program yang diterima dari controller
interface Program {
    id: number;
    name: string;
    slug: string;
}

// Menerima data program induk dari controller
const props = defineProps<{
    program: Program;
}>();

// Membuat breadcrumbs dinamis yang menyertakan nama program
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Kelola Program', href: route('admin.programs.index') },
    { title: props.program.name, href: route('admin.programs.edit', props.program.slug) },
    { title: 'Catat Pencairan Dana', href: route('admin.disbursements.create', props.program.slug) },
];
</script>

<template>

    <Head title="Catat Pencairan Dana" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6">
            <DisbursementForm :program="program" />
        </div>
    </AppLayout>
</template>
