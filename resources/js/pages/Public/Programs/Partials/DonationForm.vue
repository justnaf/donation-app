<script setup lang="ts">
import { useForm, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import axios from 'axios';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';

// --- Props ---
const props = defineProps<{
    programId: number;
    fees: Record<string, number | string>;
}>();

// --- Setup ---
const authUser = computed(() => usePage().props.auth.user);
const donationForm = useForm({
    program_id: props.programId,
    amount: 50000,
    donator_name: authUser.value?.name ?? '',
    donator_email: authUser.value?.email ?? '',
    message: '',
    is_anonymous: false,
    payment_method: 'gopay',
});

// --- Computed Properties & Helpers ---
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
const formatRupiah = (amount: number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);

const formatPaymentMethod = (method: string): string => {
    if (!method) return '';
    return method.replace(/_/g, ' ').toUpperCase();
};
// --- Fungsi Submit ---
const submitDonation = () => {
    donationForm.processing = true;
    axios.post(route('donations.store'), donationForm.data())
        .then(response => {
            const { redirect_url, order_id } = response.data;
            if (redirect_url && order_id) {
                window.open(redirect_url, '_blank');
                donationForm.reset();
                router.visit(route('donations.status', { donation: order_id }));
            } else {
                alert('Tidak bisa mendapatkan URL pembayaran.');
            }
        })
        .catch(error => {
            if (error.response?.status === 422) {
                donationForm.errors = error.response.data.errors;
            } else {
                alert(error.response?.data?.error || 'Terjadi kesalahan pada server.');
            }
        })
        .finally(() => {
            donationForm.processing = false;
        });
};
</script>

<template>
    <form @submit.prevent="submitDonation"
        class="space-y-6 rounded-xl border bg-white p-6 shadow-md">
        <div class="border-b pb-6">
            <h3
                class="font-semibold mb-4 text-lg text-center">
                Salurkan Kebaikan Anda</h3>
            <div class="space-y-4">
                <div>
                    <Label for="amount" class="mb-2">Jumlah
                        Donasi</Label>
                    <Input id="amount" type="number"
                        placeholder="Rp 50.000"
                        v-model="donationForm.amount"
                        required
                        class="text-center text-lg font-bold" />
                    <InputError
                        :message="donationForm.errors.amount"
                        class="mt-1" />
                </div>
                <div>
                    <Label for="donator_name"
                        class="mb-2">Nama Anda</Label>
                    <Input id="donator_name" type="text"
                        placeholder="Nama Lengkap"
                        v-model="donationForm.donator_name"
                        required />
                    <InputError
                        :message="donationForm.errors.donator_name"
                        class="mt-1" />
                </div>
                <div>
                    <Label for="donator_email"
                        class="mb-2">Email Anda</Label>
                    <Input id="donator_email" type="email"
                        placeholder="email@anda.com"
                        v-model="donationForm.donator_email"
                        required />
                    <InputError
                        :message="donationForm.errors.donator_email"
                        class="mt-1" />
                </div>
                <div>
                    <Label for="payment_method"
                        class="mb-2">Metode
                        Pembayaran</Label>
                    <Select
                        v-model="donationForm.payment_method">
                        <SelectTrigger>
                            <SelectValue
                                placeholder="Pilih metode..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="(fee, method) in fees"
                                :key="method"
                                :value="method">{{
                                    formatPaymentMethod(method)
                                }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div
                    class="flex items-center space-x-2 pt-2">
                    <Checkbox id="is_anonymous"
                        v-model:checked="donationForm.is_anonymous" />
                    <Label for="is_anonymous"
                        class="text-sm font-normal">Donasi
                        sebagai Hamba Allah</Label>
                </div>

                <div
                    class="mt-4 space-y-2 rounded-lg border bg-gray-50 p-3 text-sm">
                    <div class="flex justify-between">
                        <span
                            class="text-gray-600">Donasi</span>
                        <span class="font-medium">{{
                            formatRupiah(Number(donationForm.amount)
                                || 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Biaya
                            Transaksi</span>
                        <span class="font-medium">{{
                            formatRupiah(transactionFee)
                            }}</span>
                    </div>
                    <div
                        class="flex justify-between border-t pt-2 mt-2 font-bold text-base">
                        <span>Total Pembayaran</span>
                        <span>{{ formatRupiah(totalAmount)
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
</template>