<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue'; // <-- 1. Import layout baru

// Tentukan layout default untuk halaman ini
defineOptions({ layout: PublicLayout });

// Definisikan tipe data
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
interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

// Props
defineProps<{
    programs: {
        data: Program[];
        links: PaginationLink[];
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

    <Head title="Beranda - Program Donasi" />

    <section
        class="bg-white py-16 text-center sm:py-20 lg:py-24">
        <div class="container mx-auto px-4">
            <h1
                class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl lg:text-5xl">
                Satu Kebaikan, Berjuta Harapan</h1>
            <p
                class="mx-auto mt-4 max-w-2xl text-lg text-gray-600">
                Setiap donasi Anda memberikan kekuatan untuk
                mereka yang membutuhkan. Mari bergerak
                bersama membangun masa depan yang lebih
                baik.</p>
            <div class="mt-8">
                <a href="#program-donasi"
                    class="inline-block rounded-lg bg-primary px-8 py-3 text-base font-semibold text-primary-foreground shadow-lg transition hover:bg-primary/80 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                    Mulai Berdonasi
                </a>
            </div>
        </div>
    </section>

    <section id="program-donasi"
        class="py-16 sm:py-20 lg:py-24">
        <div class="container mx-auto px-4">
            <div class="mb-10 text-center">
                <h2
                    class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                    Program Pilihan Untuk Anda</h2>
                <p class="mt-2 text-md text-gray-600">
                    Salurkan kebaikan Anda melalui
                    program-program terpercaya kami.</p>
            </div>

            <div v-if="programs.data.length > 0"
                class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="program in programs.data"
                    :key="program.id"
                    class="flex flex-col overflow-hidden rounded-xl border bg-white shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <Link
                        :href="route('programs.show', program.slug)"
                        class="flex flex-col h-full">
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
                            <div
                                class="relative h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                <div class="absolute left-0 top-0 h-full rounded-full bg-green-500"
                                    :style="{ width: program.progress_percentage + '%' }">
                                </div>
                            </div>
                            <div
                                class="mt-2 flex justify-between text-sm">
                                <span
                                    class="font-medium text-gray-800">{{
                                        formatRupiah(program.collected_amount)
                                    }}</span>
                                <span
                                    class="text-gray-500">{{
                                        Math.round(program.progress_percentage)
                                    }}%</span>
                            </div>
                        </div>

                        <div
                            class="mt-4 border-t pt-3 text-xs text-gray-500">
                            Target: {{
                                formatRupiah(program.target_amount)
                            }}
                        </div>
                    </div>
                    </Link>
                </div>
            </div>

            <div v-else class="text-center text-gray-500">
                <p>Saat ini belum ada program donasi yang
                    tersedia.</p>
            </div>

            <div v-if="programs.links.length > 3"
                class="mt-10">
            </div>
        </div>
    </section>
</template>