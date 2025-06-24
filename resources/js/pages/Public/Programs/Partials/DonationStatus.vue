<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { Link } from '@inertiajs/vue3';
import { CheckCircle2, LoaderCircle, XCircle } from 'lucide-vue-next';

// --- Tipe Data ---
interface Program { name: string; slug: string; }
interface Donation {
    id: number;
    order_id: string;
    status: 'pending' | 'paid' | 'failed' | 'expired' | 'cancelled';
    program: Program;
}

// --- Props ---
// Komponen ini menerima 'donation' sebagai properti utama
const props = defineProps<{
    donation: Donation;
}>();

// --- State Internal Komponen ---
const currentStatus = ref(props.donation.status);
const pollingTimer = ref<number | null>(null);

// --- Logika Inti ---
const checkStatus = () => {
    if (currentStatus.value !== 'pending') {
        if (pollingTimer.value) clearInterval(pollingTimer.value);
        return;
    }
    axios.get(route('donations.checkStatus', { donation: props.donation.order_id }))
        .then(response => {
            currentStatus.value = response.data.status;
        });
};

onMounted(() => {
    pollingTimer.value = window.setInterval(checkStatus, 3000);
});

onUnmounted(() => {
    if (pollingTimer.value) {
        clearInterval(pollingTimer.value);
    }
});
</script>

<template>
    <div class="w-full max-w-md text-center">
        <!-- Tampilan Pending -->
        <div v-if="currentStatus === 'pending'">
            <LoaderCircle
                class="mx-auto h-16 w-16 animate-spin text-yellow-500" />
            <h1 class="mt-4 text-2xl font-bold">Menunggu
                Pembayaran</h1>
            <p class="mt-2 text-gray-600">
                Mohon selesaikan pembayaran di tab yang
                telah terbuka. Halaman ini akan diperbarui
                secara otomatis.
            </p>
            <p class="mt-4 text-sm text-gray-500">Order ID:
                {{ donation.order_id }}</p>
        </div>

        <!-- Tampilan Sukses -->
        <div v-else-if="currentStatus === 'paid'">
            <CheckCircle2
                class="mx-auto h-16 w-16 text-green-500" />
            <h1 class="mt-4 text-2xl font-bold">Terima
                Kasih!</h1>
            <p class="mt-2 text-gray-600">
                Donasi Anda untuk program <strong
                    class="font-semibold">{{
                        donation.program.name }}</strong> telah
                berhasil kami terima.
            </p>
            <Link
                :href="route('programs.show', { program: donation.program.slug })"
                class="mt-6 inline-block rounded-md bg-primary px-6 py-2 text-white">
            Kembali ke Program
            </Link>
        </div>

        <!-- Tampilan Gagal -->
        <div v-else>
            <XCircle
                class="mx-auto h-16 w-16 text-red-500" />
            <h1 class="mt-4 text-2xl font-bold">Pembayaran
                Gagal</h1>
            <p class="mt-2 text-gray-600">
                Pembayaran untuk donasi ini gagal,
                dibatalkan, atau telah kedaluwarsa.
            </p>
            <Link
                :href="route('programs.show', { program: donation.program.slug })"
                class="mt-6 inline-block rounded-md bg-primary px-6 py-2 text-white">
            Coba Lagi
            </Link>
        </div>
    </div>
</template>