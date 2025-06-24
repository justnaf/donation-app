<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import ProgramListSection from './Partials/ProgramListSection.vue';

// Tentukan layout default untuk halaman ini
defineOptions({ layout: PublicLayout });

// Definisikan tipe data yang dibutuhkan oleh ProgramListSection
interface Program {
    id: number;
    name: string;
    slug: string;
    poster_url: string;
    target_amount: number;
    collected_amount: number;
    progress_percentage: number;
}
interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

// Terima props yang akan diteruskan ke partial
const props = defineProps<{
    programs: {
        data: Program[];
        links: PaginationLink[];
    };
}>();
</script>

<template>

    <Head title="Beranda - Program Donasi" />

    <!-- Hero Section -->
    <section
        class="bg-white py-16 text-center sm:py-20 lg:py-24">
        <div class="container mx-auto px-4">
            <h1
                class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl lg:text-5xl">
                Satu Kebaikan, Berjuta Harapan
            </h1>
            <p
                class="mx-auto mt-4 max-w-2xl text-lg text-gray-600">
                Setiap donasi Anda memberikan kekuatan untuk
                mereka yang membutuhkan. Mari bergerak
                bersama membangun masa depan yang lebih
                baik.
            </p>
            <div class="mt-8">
                <!-- Tombol disesuaikan dengan warna utama brand -->
                <a href="#program-donasi"
                    class="inline-block rounded-lg bg-[#F08519] px-8 py-3 text-base font-semibold text-white shadow-lg transition hover:bg-[#F08519]/90 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#F08519]">
                    Mulai Berdonasi
                </a>
            </div>
        </div>
    </section>

    <!-- Panggil Komponen Daftar Program -->
    <ProgramListSection :programs="props.programs" />

</template>
