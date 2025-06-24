<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import DonationSidebar from './Partials/DonationSidebar.vue';
import ProgramDetails from './Partials/ProgramDetails.vue';

defineOptions({ layout: PublicLayout });

interface Category { id: number; name: string; slug: string; }
interface Donation { id: number; donator_name: string; amount: number; created_at: string; is_anonymous: boolean; message: string; }
interface ProgramUpdate { id: number; title: string; content: string; created_at: string; }
interface Disbursement { id: number; description: string; amount: number; disbursed_at: string; }
interface Program {
    id: number;
    name: string;
    slug: string;
    poster_url: string;
    status: string;
    target_amount: number;
    collected_amount: number;
    progress_percentage: number;
    start_date: string | null;
    end_date: string | null;
    short_description: string;
    content: string; category: Category | null;
    donations: Donation[]; updates: ProgramUpdate[]; disbursements: Disbursement[];
}

defineProps<{
    program: Program;
    fees: Record<string, number | string>;
}>();
</script>

<template>

    <Head :title="program.name"
        :description="program.short_description" />

    <div class="container mx-auto max-w-7xl px-4 py-6">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <ProgramDetails :program="program" />
            <DonationSidebar :program="program"
                :fees="fees" />
        </div>
    </div>
</template>