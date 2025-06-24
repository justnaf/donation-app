<script setup lang="ts">
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

// Tipe Data
interface Category { id: number; name: string; slug: string; }
interface Donation { id: number; donator_name: string; amount: number; created_at: string; is_anonymous: boolean; message: string | null; }
interface ProgramUpdate { id: number; title: string; content: string; created_at: string; }
interface Disbursement { id: number; description: string; amount: number; disbursed_at: string; }
interface Program {
    id: number;
    name: string;
    slug: string;
    poster_url: string;
    content: string;
    category: Category | null;
    updates: ProgramUpdate[];
    disbursements: Disbursement[];
    donations: Donation[];
}

// Props: Komponen ini menerima data program
const props = defineProps<{
    program: Program;
}>();

// State internal untuk mengelola tab yang aktif
const activeTab = ref('about');

// Fungsi Helper
const formatDate = (dateString: string | null) => {
    if (!dateString) return 'Tanpa Batas';
    return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};
const donatorDisplayName = (donation: Donation) => donation.is_anonymous ? 'Hamba Allah' : donation.donator_name;
const formatRupiah = (amount: number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
</script>

<template>
    <div class="lg:col-span-2">
        <!-- Poster dengan Judul -->
        <div
            class="relative mb-6 w-full overflow-hidden rounded-xl shadow-lg">
            <img :src="program.poster_url"
                :alt="program.name"
                class="aspect-video w-full object-cover" />
            <div
                class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent">
            </div>
            <div class="absolute bottom-0 left-0 p-6">
                <Link v-if="program.category"
                    :href="route('programs.index', { category: program.category.slug })"
                    class="mb-2 inline-block rounded-full bg-white/20 px-3 py-1 text-xs font-medium text-white backdrop-blur-sm transition hover:bg-white/30">
                {{ program.category.name }}
                </Link>
                <h1
                    class="text-2xl font-bold text-white shadow-lg md:text-4xl">
                    {{ program.name }}</h1>
            </div>
        </div>

        <!-- Sistem Tab untuk Detail -->
        <div
            class="rounded-lg border bg-white p-6 shadow-md">
            <!-- Navigasi Tab -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-6"
                    aria-label="Tabs">
                    <button @click="activeTab = 'about'"
                        :class="[activeTab === 'about' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
                        class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium">Tentang
                        Program</button>
                    <button @click="activeTab = 'updates'"
                        :class="[activeTab === 'updates' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
                        class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium">Kabar
                        Terbaru</button>
                    <button
                        @click="activeTab = 'disbursements'"
                        :class="[activeTab === 'disbursements' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
                        class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium">Penggunaan
                        Dana</button>
                    <button @click="activeTab = 'messages'"
                        :class="[activeTab === 'messages' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
                        class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium">Pesan
                        Donatur</button>
                </nav>
            </div>

            <!-- Konten Tab -->
            <div class="py-6">
                <!-- Tab: Tentang Program -->
                <div v-if="activeTab === 'about'"
                    class="prose max-w-none dark:prose-invert"
                    v-html="program.content"></div>

                <!-- Tab: Kabar Terbaru -->
                <div v-if="activeTab === 'updates'"
                    class="space-y-6">
                    <div v-if="!program.updates || program.updates.length === 0"
                        class="text-center text-gray-500 py-8">
                        Belum ada kabar terbaru untuk
                        program ini.</div>
                    <article v-else
                        v-for="update in program.updates"
                        :key="update.id"
                        class="border-b pb-4 last:border-b-0">
                        <p class="text-sm text-gray-500">{{
                            formatDate(update.created_at) }}
                        </p>
                        <h4 class="font-semibold text-lg">{{
                            update.title }}</h4>
                        <p class="mt-2 text-gray-700">{{
                            update.content }}</p>
                    </article>
                </div>

                <!-- Tab: Penggunaan Dana -->
                <div v-if="activeTab === 'disbursements'"
                    class="space-y-4">
                    <div v-if="!program.disbursements || program.disbursements.length === 0"
                        class="text-center text-gray-500 py-8">
                        Belum ada laporan penggunaan dana.
                    </div>
                    <div v-else
                        v-for="disbursement in program.disbursements"
                        :key="disbursement.id"
                        class="flex items-center justify-between rounded-lg border p-4">
                        <div>
                            <p class="font-medium">{{
                                disbursement.description }}
                            </p>
                            <p
                                class="text-sm text-gray-500">
                                {{
                                    formatDate(disbursement.disbursed_at)
                                }}</p>
                        </div>
                        <p
                            class="font-semibold text-lg text-primary">
                            {{
                                formatRupiah(disbursement.amount)
                            }}</p>
                    </div>
                </div>

                <!-- Tab: Pesan Donatur -->
                <div v-if="activeTab === 'messages'"
                    class="space-y-6">
                    <div v-if="program.donations.length === 0"
                        class="text-center text-gray-500 py-8">
                        <p>Belum ada pesan dan doa
                            dari para donatur untuk
                            program ini.</p>
                    </div>
                    <div v-else class="space-y-4">
                        <article
                            v-for="donation in program.donations"
                            :key="`msg-${donation.id}`"
                            class="rounded-lg border bg-gray-50/50 p-4 shadow-sm dark:bg-gray-800/50">
                            <div
                                class="flex items-start gap-4">
                                <span
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200 text-lg font-bold text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    {{
                                        donatorDisplayName(donation).charAt(0)
                                    }}
                                </span>
                                <div class="flex-1">
                                    <div
                                        class="flex items-center justify-between">
                                        <p
                                            class="font-semibold text-gray-900 dark:text-white">
                                            {{
                                                donatorDisplayName(donation)
                                            }}</p>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400">
                                            {{
                                                formatDate(donation.created_at)
                                            }}</p>
                                    </div>
                                    <blockquote
                                        class="mt-2 text-sm italic text-gray-600 dark:text-gray-300">
                                        "{{
                                            donation.message
                                        }}"
                                    </blockquote>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>