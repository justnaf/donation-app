<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ProgramForm from './Partials/ProgramForm.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

// Definisikan tipe untuk program yang diterima dari controller
type ProgramStatus = 'draft' | 'active' | 'ended';
interface Program {
    id: number;
    name: string;
    slug: string;
    poster_url: string;
    status: ProgramStatus;
    target_amount: number;
    collected_amount: number;
    start_date: string | null;
    end_date: string | null;
    short_description: string;
    content: string;
}

const props = defineProps<{
    program: Program;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Kelola Program Donasi', href: route('admin.programs.index') },
    { title: props.program.name, href: route('admin.programs.edit', props.program.id) },
    { title: 'Edit', href: route('admin.programs.edit', props.program.id) },
];
</script>

<template>

    <Head :title="`Edit Program: ${program.name}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="rounded-xl p-4">
            <ProgramForm :program="program" />
        </div>
    </AppLayout>
</template>