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

// Props
defineProps<{
    program: Program;
}>();

// State
const activeTab = ref('about');

// Helper Functions
const formatDate = (dateString: string | null) => {
    if (!dateString) return 'Tanpa Batas';
    return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};
const donatorDisplayName = (donation: Donation) => donation.is_anonymous ? 'Hamba Allah' : donation.donator_name;
const formatRupiah = (amount: number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
</script>

<template>
    <div class="lg:col-span-2">
        <div
            class="relative mb-6 w-full overflow-hidden rounded-xl shadow-lg group">
            <img :src="program.poster_url"
                :alt="program.name"
                class="aspect-video w-full object-cover transition-transform duration-500 group-hover:scale-105" />

            <div
                class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent">
            </div>

            <div
                class="absolute bottom-0 left-0 p-4 md:p-6 w-full">
                <Link v-if="program.category"
                    :href="route('programs.index', { category: program.category.slug })"
                    class="mb-2 inline-block rounded-full bg-[#F08519]/80 px-3 py-1 text-[10px] md:text-xs font-medium text-white backdrop-blur-sm transition hover:bg-[#F08519]/60">
                    {{ program.category.name }}
                </Link>
                <h1
                    class="text-xl md:text-3xl lg:text-4xl font-bold text-white shadow-lg leading-tight">
                    {{ program.name }}
                </h1>
            </div>
        </div>

        <div
            class="rounded-lg border bg-white shadow-md overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-4 md:space-x-6 overflow-x-auto px-4 md:px-6 scrollbar-hide"
                    aria-label="Tabs">
                    <button v-for="(label, key) in {
                        about: 'Tentang Program',
                        updates: 'Kabar Terbaru',
                        disbursements: 'Penggunaan Dana',
                        messages: 'Pesan Donatur'
                    }" :key="key"
                        @click="activeTab = key" :class="[
                            activeTab === key
                                ? 'border-primary text-primary'
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                        ]"
                        class="whitespace-nowrap border-b-2 py-3 md:py-4 text-sm font-medium transition-colors duration-200">
                        {{ label }}
                    </button>
                </nav>
            </div>

            <div class="p-4 md:p-6">

                <div v-if="activeTab === 'about'"
                    class="prose prose-sm md:prose-base max-w-none text-gray-700 dark:prose-invert"
                    v-html="program.content">
                </div>

                <div v-if="activeTab === 'updates'"
                    class="space-y-6">
                    <div v-if="!program.updates || program.updates.length === 0"
                        class="text-center text-gray-500 py-8 text-sm md:text-base">
                        Belum ada kabar terbaru untuk
                        program ini.
                    </div>
                    <article v-else
                        v-for="update in program.updates"
                        :key="update.id"
                        class="border-b pb-4 last:border-b-0">
                        <p
                            class="text-xs md:text-sm text-gray-500 mb-1">
                            {{ formatDate(update.created_at)
                            }}
                        </p>
                        <h4
                            class="font-semibold text-base md:text-lg text-gray-900 mb-2">
                            {{ update.title }}
                        </h4>
                        <p
                            class="text-sm md:text-base text-gray-700 leading-relaxed">
                            {{ update.content }}
                        </p>
                    </article>
                </div>

                <div v-if="activeTab === 'disbursements'"
                    class="space-y-4">
                    <div v-if="!program.disbursements || program.disbursements.length === 0"
                        class="text-center text-gray-500 py-8 text-sm">
                        Belum ada laporan penggunaan dana.
                    </div>
                    <div v-else
                        v-for="disbursement in program.disbursements"
                        :key="disbursement.id"
                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-4 rounded-lg border p-3 md:p-4 hover:bg-gray-50 transition">
                        <div>
                            <p
                                class="font-medium text-gray-900 text-sm md:text-base">
                                {{ disbursement.description
                                }}
                            </p>
                            <p
                                class="text-xs text-gray-500 mt-1">
                                {{
                                    formatDate(disbursement.disbursed_at)
                                }}
                            </p>
                        </div>
                        <p
                            class="font-semibold text-base md:text-lg text-primary self-end sm:self-auto">
                            - {{
                                formatRupiah(disbursement.amount)
                            }}
                        </p>
                    </div>
                </div>

                <div v-if="activeTab === 'messages'"
                    class="space-y-4 md:space-y-6">
                    <div v-if="!program.donations || program.donations.length === 0"
                        class="text-center text-gray-500 py-8 text-sm">
                        <p>Belum ada pesan dan doa dari para
                            donatur.</p>
                    </div>
                    <div v-else
                        class="space-y-3 md:space-y-4">
                        <article
                            v-for="donation in program.donations"
                            :key="`msg-${donation.id}`"
                            class="rounded-lg border bg-gray-50/50 p-3 md:p-4 shadow-sm dark:bg-gray-800/50">
                            <div
                                class="flex items-start gap-3 md:gap-4">
                                <span
                                    class="flex h-8 w-8 md:h-10 md:w-10 shrink-0 items-center justify-center rounded-full bg-gray-200 text-sm md:text-lg font-bold text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    {{
                                    donatorDisplayName(donation).charAt(0)
                                    }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                        <p
                                            class="font-semibold text-sm md:text-base text-gray-900 dark:text-white truncate">
                                            {{
                                            donatorDisplayName(donation)
                                            }}
                                        </p>
                                        <p
                                            class="text-[10px] md:text-xs text-gray-500 whitespace-nowrap">
                                            {{
                                            formatDate(donation.created_at)
                                            }}
                                        </p>
                                    </div>
                                    <blockquote
                                        v-if="donation.message"
                                        class="mt-2 text-sm italic text-gray-600 dark:text-gray-300 leading-snug">
                                        "{{ donation.message
                                        }}"
                                    </blockquote>
                                    <p v-else
                                        class="mt-1 text-xs text-gray-400 italic">
                                        (Tidak ada pesan)
                                    </p>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>
/* Opsional: Menyembunyikan scrollbar default tapi tetap bisa discroll */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>