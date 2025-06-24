<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

// Tipe data untuk satu program
interface Program {
    id: number;
    name: string;
    slug: string;
    poster_url: string;
    target_amount: number;
    collected_amount: number;
    progress_percentage: number;
}

// Komponen ini menerima 'program' sebagai prop
defineProps<{
    program: Program;
}>();

// Helper untuk format mata uang
const formatRupiah = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount);
};
</script>

<template>
    <div
        class="flex flex-col overflow-hidden rounded-xl border bg-white shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
        <Link :href="route('programs.show', program.slug)"
            class="flex h-full flex-col">
        <div class="relative aspect-video">
            <img :src="program.poster_url"
                :alt="program.name"
                class="h-full w-full object-cover" />
        </div>
        <div class="flex flex-1 flex-col p-4">
            <h3
                class="font-semibold text-gray-900 line-clamp-2">
                {{ program.name }}</h3>

            <div class="mt-4 flex-grow">
                <!-- Progress Bar -->
                <div
                    class="relative h-2 w-full overflow-hidden rounded-full bg-orange-100 dark:bg-gray-700">
                    <div class="absolute left-0 top-0 h-full rounded-full bg-[#F08519]"
                        :style="{ width: program.progress_percentage + '%' }">
                    </div>
                </div>
                <div
                    class="mt-2 flex justify-between text-sm">
                    <span class="font-bold text-[#F08519]">
                        {{
                            formatRupiah(program.collected_amount)
                        }}
                    </span>
                    <span class="text-gray-500">{{
                        Math.round(program.progress_percentage)
                        }}%</span>
                </div>
            </div>

            <div
                class="mt-4 border-t pt-3 text-xs text-gray-500">
                Target: {{
                    formatRupiah(program.target_amount) }}
            </div>
        </div>
        </Link>
    </div>
</template>