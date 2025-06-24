<script setup lang="ts">
import ProgramCard from './ProgramCard.vue';
import Pagination from '@/components/Pagination.vue'; // Menggunakan komponen Pagination yang sudah ada

// Tipe data yang dibutuhkan oleh komponen ini
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

// Props: Menerima objek paginasi dari parent
defineProps<{
    programs: {
        data: Program[];
        links: PaginationLink[];
    };
}>();
</script>

<template>
    <section id="program-donasi"
        class="py-16 sm:py-20 lg:py-24">
        <div class="container mx-auto px-4">
            <div class="mb-10 text-center">
                <h2
                    class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                    Program Pilihan Untuk Anda
                </h2>
                <p class="mt-2 text-md text-gray-600">
                    Salurkan kebaikan Anda melalui
                    program-program terpercaya kami.
                </p>
            </div>

            <div v-if="programs.data.length > 0"
                class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Gunakan komponen ProgramCard di sini -->
                <ProgramCard
                    v-for="program in programs.data"
                    :key="program.id" :program="program" />
            </div>

            <div v-else class="text-center text-gray-500">
                <p>Saat ini belum ada program donasi yang
                    tersedia.</p>
            </div>

            <!-- Gunakan komponen Pagination -->
            <div class="mt-10 flex justify-center">
                <Pagination :links="programs.links" />
            </div>
        </div>
    </section>
</template>