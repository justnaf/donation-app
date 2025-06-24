<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';

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

        <div
            class="mb-8 flex flex-wrap items-center justify-center gap-2">
            <Link :href="route('programs.index')" :class="{
                'bg-primary text-primary-foreground': !filters.category,
                'bg-gray-200 text-gray-700 hover:bg-gray-300': filters.category
            }"
                class="rounded-full px-4 py-2 text-sm font-medium transition">
            Semua
            </Link>
            <Link v-for="category in categories"
                :key="category.id"
                :href="route('programs.index', { category: category.slug })"
                :class="{
                    'bg-primary text-primary-foreground': filters.category === category.slug,
                    'bg-gray-200 text-gray-700 hover:bg-gray-300': filters.category !== category.slug
                }"
                class="rounded-full px-4 py-2 text-sm font-medium transition">
            {{ category.name }}
            </Link>
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
                            <span class="text-gray-500">{{
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

        <div v-else class="py-16 text-center text-gray-500">
            <p class="text-lg">Oops! Tidak ada program
                donasi yang ditemukan.</p>
            <p class="mt-2">Coba hapus filter atau periksa
                kategori lainnya.</p>
        </div>

        <div class="flex justify-center mt-10">
        </div>
    </div>
</template>