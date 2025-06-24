<script setup lang="ts">
import DonationForm from './DonationForm.vue';
import { Progress } from '@/components/ui/progress';

interface Donation { id: number; donator_name: string; amount: number; is_anonymous: boolean; }
interface Program {
    id: number;
    target_amount: number;
    collected_amount: number;
    progress_percentage: number;
    donations: Donation[];
}

defineProps<{
    program: Program;
    fees: Record<string, number | string>;
}>();

const formatRupiah = (amount: number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
const donatorDisplayName = (donation: Donation) => donation.is_anonymous ? 'Hamba Allah' : donation.donator_name;
</script>

<template>
    <div class="lg:sticky lg:top-24 h-fit space-y-6">
        <!-- Progress Bar & Info Finansial -->
        <div
            class="rounded-xl border bg-white p-6 shadow-md">
            <div
                class="flex justify-between text-lg font-bold">
                <span class="text-gray-600">Terkumpul</span>
                <!-- Warna disesuaikan dengan warna utama -->
                <span class="text-[#F08519]">{{
                    formatRupiah(program.collected_amount)
                }}</span>
            </div>
            <!-- Latar belakang progress bar disesuaikan -->
            <Progress
                :model-value="program.progress_percentage"
                class="my-2 h-2.5 bg-orange-100" />
            <div
                class="flex justify-between text-sm text-gray-500">
                <span>Target</span>
                <span>{{ formatRupiah(program.target_amount)
                }}</span>
            </div>
        </div>

        <!-- Form Donasi (Komponen Terpisah) -->
        <DonationForm :program-id="program.id"
            :fees="fees" />

        <!-- Daftar Donatur -->
        <div
            class="rounded-lg border bg-white p-6 shadow-md">
            <h3 class="font-semibold mb-2">Donatur Terbaru
                ({{ program.donations.length }})</h3>
            <div v-if="program.donations.length === 0"
                class="text-center text-sm text-gray-500 py-4">
                Jadilah donatur pertama!</div>
            <ul v-else
                class="space-y-3 max-h-60 overflow-y-auto pr-2">
                <li v-for="donation in program.donations"
                    :key="donation.id"
                    class="flex items-center justify-between text-sm">
                    <span class="font-medium">{{
                        donatorDisplayName(donation)
                    }}</span>
                    <!-- Warna disesuaikan dengan warna utama -->
                    <span
                        class="font-bold text-[#F08519]">{{
                            formatRupiah(donation.amount)
                        }}</span>
                </li>
            </ul>
        </div>
    </div>
</template>
