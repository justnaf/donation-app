<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import ProgramCard from '../Partials/ProgramCard.vue';

// Tentukan layout default untuk halaman ini
defineOptions({ layout: PublicLayout });

// Definisikan tipe data yang diterima dari controller
interface Category {
    id: number;
    name: string;
    slug: string;
}
interface Program {
    id: number;
    name: string;
    slug: string;
    short_description: string;
    poster_url: string;
    target_amount: number;
    collected_amount: number;
    progress_percentage: number;
}
interface PaginatedPrograms {
    data: Program[];
    links: { url: string | null; label: string; active: boolean; }[];
}

const props = defineProps<{
    programs: PaginatedPrograms;
    categories: Category[];
    filters: {
        category: string | null;
    };
}>();

// Helper
const formatRupiah = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount);
};
</script>

<template>

    <Head title="Semua Program Donasi" />

    <div class="container mx-auto px-4 py-8">
        <div class="mb-10 text-center">
            <h1
                class="text-3xl font-bold tracking-tight md:text-4xl">
                Temukan Program Kebaikan</h1>
            <p class="mt-2 text-muted-foreground">Setiap
                bantuan Anda, sekecil apapun, akan menjadi
                harapan baru bagi mereka.</p>
        </div>

        <!-- Filter Kategori dengan Warna Brand -->
        <div
            class="mb-8 flex flex-wrap items-center justify-center gap-2">
            <Link :href="route('programs.index')" :class="{
                'bg-[#F08519] text-white border-[#F08519]': !filters.category,
                'bg-white text-gray-700 hover:bg-gray-100 border-gray-300': filters.category,
            }"
                class="rounded-full border px-4 py-2 text-sm font-medium transition">
            Semua
            </Link>
            <Link v-for="category in categories"
                :key="category.id"
                :href="route('programs.index', { category: category.slug })"
                :class="{
                    'bg-[#F08519] text-white border-[#F08519]': filters.category === category.slug,
                    'bg-white text-gray-700 hover:bg-gray-100 border-gray-300': filters.category !== category.slug,
                }"
                class="rounded-full border px-4 py-2 text-sm font-medium transition">
            {{ category.name }}
            </Link>
        </div>

        <!-- Grid Program -->
        <div v-if="programs.data.length > 0"
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <ProgramCard v-for="program in programs.data"
                :key="program.id" :program="program" />
        </div>

        <!-- Pesan Kosong -->
        <div v-else class="py-16 text-center text-gray-500">
            <p class="text-lg">Oops! Tidak ada program
                donasi yang ditemukan.</p>
            <p class="mt-2">Coba hapus filter atau periksa
                kategori lainnya.</p>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-10">
            <!-- Asumsi Anda memiliki komponen Pagination.vue -->
            <!-- <Pagination :links="programs.links" /> -->
        </div>
    </div>
</template>