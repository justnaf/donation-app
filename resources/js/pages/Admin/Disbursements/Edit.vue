<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DisbursementForm from './Partials/DisbursementForm.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

// Tipe Data untuk mendefinisikan struktur props
interface Program {
    id: number;
    name: string;
    slug: string;
}

interface Disbursement {
    id: number;
    amount: number;
    description: string;
    disbursed_at: string;
    program: Program; // Relasi ke program induk
}

// Menerima data 'disbursement' dari controller
const props = defineProps<{
    disbursement: Disbursement;
}>();

// Membuat breadcrumbs dinamis
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Kelola Program', href: route('admin.programs.index') },
    { title: props.disbursement.program.name, href: route('admin.programs.edit', props.disbursement.program.slug) },
    { title: 'Edit Pencairan Dana', href: route('admin.disbursements.edit', props.disbursement.id) },
];
</script>

<template>

    <Head
        :title="`Edit Pencairan: ${disbursement.description.substring(0, 30)}...`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6">
            <DisbursementForm
                :program="disbursement.program"
                :disbursement="disbursement" />
        </div>
    </AppLayout>
</template>
