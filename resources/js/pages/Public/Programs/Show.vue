<script setup lang="ts">
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { ref, computed } from 'vue';
import axios from 'axios';
import { Progress } from '@/components/ui/progress';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';


defineOptions({ layout: PublicLayout });
interface Category { id: number; name: string; slug: string; }
interface Donation { id: number; donator_name: string; amount: number; created_at: string; is_anonymous: boolean; }
interface ProgramUpdate { id: number; title: string; content: string; created_at: string; }
interface Disbursement { id: number; description: string; amount: number; disbursed_at: string; }
interface Program {
    id: number;
    name: string;
    slug: string;
    poster_url: string;
    status: string;
    target_amount: number;
    collected_amount: number;
    progress_percentage: number;
    start_date: string | null;
    end_date: string | null;
    short_description: string;
    content: string;
    category: Category | null;
    donations: Donation[];
    updates: ProgramUpdate[];
    disbursements: Disbursement[];
}

const props = defineProps<{
    program: Program;
    fees: Record<string, number | string>;
}>();

const activeTab = ref('about');
const authUser = computed(() => usePage().props.auth.user);

const donationForm = useForm({
    program_id: props.program.id,
    amount: 50000,
    donator_name: authUser.value?.name ?? '',
    donator_email: authUser.value?.email ?? '',
    message: '',
    is_anonymous: false,
    payment_method: 'other_qris',
});

// --- Computed Properties untuk Biaya & Total ---
const transactionFee = computed(() => {
    const feeRule = props.fees[donationForm.payment_method];
    const amount = Number(donationForm.amount) || 0;

    if (typeof feeRule === 'number') return feeRule;
    if (typeof feeRule === 'string' && feeRule.includes('%')) {
        const percentage = parseFloat(feeRule.replace('%', ''));
        if (isNaN(percentage)) return 0;
        return Math.ceil((amount * percentage) / 100);
    }
    return 0;
});

const totalAmount = computed(() => {
    return (Number(donationForm.amount) || 0) + transactionFee.value;
});

const submitDonation = () => {
    donationForm.processing = true;

    axios.post(route('donations.store'), donationForm.data())
        .then(response => {
            const redirectUrl = response.data.redirect_url;
            console.log(response);
            if (redirectUrl) {
                window.open(redirectUrl, '_blank');
                donationForm.reset();
            } else {
                alert('Tidak bisa mendapatkan URL pembayaran. Silakan coba lagi.');
            }
        })
        .catch(error => {
            if (error.response && error.response.status === 422) {
                donationForm.errors = error.response.data.errors;
            } else {
                const errorMessage = error.response?.data?.error || 'Terjadi kesalahan pada server.';
                alert(errorMessage);
            }
        })
        .finally(() => {
            donationForm.processing = false;
        });
};

const formatRupiah = (amount: number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
const formatDate = (dateString: string | null) => {
    if (!dateString) return 'Tanpa Batas';
    return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};
const donatorDisplayName = (donation: Donation) => donation.is_anonymous ? 'Hamba Allah' : donation.donator_name;

const formatPaymentMethod = (method: string): string => {
    if (!method) return '';
    return method.replace(/_/g, ' ').toUpperCase();
};
</script>

<template>

    <Head :title="program.name"
        :description="program.short_description" />

    <div class="container mx-auto max-w-7xl px-4 py-6">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

            <div class="lg:col-span-2">
                <div
                    class="relative mb-6 w-full overflow-hidden rounded-xl shadow-lg">
                    <img :src="program.poster_url"
                        :alt="program.name"
                        class="aspect-video w-full object-cover" />
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 p-6">
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

                <div
                    class="rounded-lg border bg-white p-6 shadow-md">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-6"
                            aria-label="Tabs">
                            <button
                                @click="activeTab = 'about'"
                                :class="[activeTab === 'about' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
                                class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium">Tentang
                                Program</button>
                            <button
                                @click="activeTab = 'updates'"
                                :class="[activeTab === 'updates' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
                                class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium">Kabar
                                Terbaru</button>
                            <button
                                @click="activeTab = 'disbursements'"
                                :class="[activeTab === 'disbursements' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700']"
                                class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium">Penggunaan
                                Dana</button>
                        </nav>
                    </div>

                    <div class="py-6">
                        <div v-if="activeTab === 'about'"
                            class="prose max-w-none dark:prose-invert"
                            v-html="program.content"></div>
                        <div v-if="activeTab === 'updates'"
                            class="space-y-6">
                            <div v-if="program.updates.length === 0"
                                class="text-center text-gray-500 py-8">
                                Belum ada kabar terbaru
                                untuk program ini.</div>
                            <article v-else
                                v-for="update in program.updates"
                                :key="update.id"
                                class="border-b pb-4 last:border-b-0">
                                <p
                                    class="text-sm text-gray-500">
                                    {{
                                        formatDate(update.created_at)
                                    }}</p>
                                <h4
                                    class="font-semibold text-lg">
                                    {{ update.title }}</h4>
                                <p
                                    class="mt-2 text-gray-700">
                                    {{ update.content }}</p>
                            </article>
                        </div>
                        <div v-if="activeTab === 'disbursements'"
                            class="space-y-4">
                            <div v-if="program.disbursements.length === 0"
                                class="text-center text-gray-500 py-8">
                                Belum ada laporan penggunaan
                                dana.</div>
                            <div v-else
                                v-for="disbursement in program.disbursements"
                                :key="disbursement.id"
                                class="flex items-center justify-between rounded-lg border p-4">
                                <div>
                                    <p class="font-medium">
                                        {{
                                            disbursement.description
                                        }}</p>
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
                    </div>
                </div>
            </div>

            <div class="lg:sticky lg:top-24 h-fit">
                <form @submit.prevent="submitDonation"
                    class="space-y-6 rounded-xl border bg-white p-6 shadow-md">
                    <div>
                        <div
                            class="flex justify-between text-lg font-bold">
                            <span
                                class="text-gray-600">Terkumpul</span><span>{{
                                    formatRupiah(program.collected_amount)
                                }}</span>
                        </div>
                        <Progress
                            :model-value="program.progress_percentage"
                            class="my-2 h-2.5" />
                        <div
                            class="flex justify-between text-sm text-gray-500">
                            <span>Target</span><span>{{
                                formatRupiah(program.target_amount)
                                }}</span>
                        </div>
                    </div>

                    <div class="border-t pt-6">
                        <h3
                            class="font-semibold mb-4 text-lg text-center">
                            Salurkan Kebaikan Anda</h3>
                        <div class="space-y-4">
                            <div><Label for="amount"
                                    class="mb-2">Jumlah
                                    Donasi</Label><Input
                                    id="amount"
                                    type="number"
                                    placeholder="Rp 50.000"
                                    v-model="donationForm.amount"
                                    required
                                    class="text-center text-lg font-bold" />
                                <InputError
                                    :message="donationForm.errors.amount"
                                    class="mt-1" />
                            </div>
                            <div><Label for="donator_name"
                                    class="mb-2">Nama
                                    Anda</Label><Input
                                    id="donator_name"
                                    type="text"
                                    placeholder="Nama Lengkap"
                                    v-model="donationForm.donator_name"
                                    required />
                                <InputError
                                    :message="donationForm.errors.donator_name"
                                    class="mt-1" />
                            </div>
                            <div><Label for="donator_email"
                                    class="mb-2">Email
                                    Anda</Label><Input
                                    id="donator_email"
                                    type="email"
                                    placeholder="email@anda.com"
                                    v-model="donationForm.donator_email"
                                    required />
                                <InputError
                                    :message="donationForm.errors.donator_email"
                                    class="mt-1" />
                            </div>
                            <div><Label for="payment_method"
                                    class="mb-2">Metode
                                    Pembayaran</Label><Select
                                    v-model="donationForm.payment_method">
                                    <SelectTrigger>
                                        <SelectValue
                                            placeholder="Pilih metode..." />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="(fee, method) in fees"
                                            :key="method"
                                            :value="method">
                                            {{
                                                formatPaymentMethod(method)
                                            }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select></div>
                            <div
                                class="flex items-center space-x-2 pt-2">
                                <Checkbox id="is_anonymous"
                                    v-model:checked="donationForm.is_anonymous" />
                                <Label for="is_anonymous"
                                    class="text-sm font-normal">Donasi
                                    sebagai Hamba
                                    Allah</Label>
                            </div>

                            <div
                                class="mt-4 space-y-2 rounded-lg border bg-gray-50 p-3 text-sm">
                                <div
                                    class="flex justify-between">
                                    <span
                                        class="text-gray-600">Donasi</span><span
                                        class="font-medium">{{
                                            formatRupiah(Number(donationForm.amount)
                                                || 0) }}</span>
                                </div>
                                <div
                                    class="flex justify-between">
                                    <span
                                        class="text-gray-600">Biaya
                                        Transaksi</span><span
                                        class="font-medium">{{
                                            formatRupiah(transactionFee)
                                        }}</span>
                                </div>
                                <div
                                    class="flex justify-between border-t pt-2 mt-2 font-bold text-base">
                                    <span>Total
                                        Pembayaran</span><span>{{
                                            formatRupiah(totalAmount)
                                        }}</span>
                                </div>
                            </div>

                            <Button type="submit"
                                class="w-full h-12 text-lg"
                                :disabled="donationForm.processing">Lanjutkan
                                Pembayaran</Button>
                        </div>
                    </div>
                </form>

                <div
                    class="mt-6 rounded-lg border bg-white p-6 shadow-md">
                    <h3 class="font-semibold mb-2">Donatur
                        ({{ program.donations.length }})
                    </h3>
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
                            <span
                                class="font-bold text-primary">{{
                                    formatRupiah(donation.amount)
                                }}</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</template>