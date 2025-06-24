<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import DonationStatus from './Partials/DonationStatus.vue';

defineOptions({ layout: PublicLayout });

interface Program { name: string; slug: string; }
interface Donation {
    id: number;
    order_id: string;
    status: 'pending' | 'paid' | 'failed' | 'expired' | 'cancelled';
    program: Program;
}

const props = defineProps<{
    donation: Donation;
}>();
</script>

<template>

    <Head :title="`Status Donasi ${donation.order_id}`" />

    <div
        class="container mx-auto flex min-h-[60vh] items-center justify-center px-4 py-8">
        <DonationStatus :donation="props.donation" />
    </div>
</template>