<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import Pagination from '@/components/Pagination.vue';

type DonationStatus = 'paid' | 'pending' | 'expired' | 'failed' | 'cancelled';
interface Donation {
    id: number;
    order_id: string;
    donator_name: string;
    amount: number;
    status: DonationStatus;
    payment_method: string;
    created_at: string;
}
interface Program {
    id: number;
    name: string;
    slug: string;
}
interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

// Props
const props = defineProps<{
    program: Program;
    donations: {
        data: Donation[];
        links: PaginationLink[];
    };
    filters: {
        search: string;
        status: string;
    };
}>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Kelola Program Donasi', href: route('admin.programs.index') },
    { title: props.program.name, href: route('admin.programs.edit', props.program.slug) },
    { title: 'Daftar Donasi', href: route('admin.programs.donations.index', props.program.slug) },
];

// State untuk filter
const search = ref(props.filters.search);
const status = ref(props.filters.status);

// Watcher untuk filter, menggunakan satu objek untuk semua filter
watch([search, status], ([searchValue, statusValue]) => {
    router.get(
        route('admin.programs.donations.index', props.program.slug),
        {
            search: searchValue,
            status: statusValue,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}, { deep: true });

// Helper untuk Badge Status
const statusBadgeClass = (status: DonationStatus) => {
    return {
        paid: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        expired: 'bg-gray-100 text-gray-800',
        failed: 'bg-red-100 text-red-800',
        cancelled: 'bg-red-100 text-red-800',
    }[status];
};

const formatRupiah = (amount: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
};

const formatDateTime = (dateString: string) => {
    return new Date(dateString).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
};
</script>

<template>

    <Head :title="`Daftar Donasi - ${program.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-col gap-6">
                <div
                    class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="text-2xl font-bold">
                            Daftar
                            Donasi</h2>
                        <p class="text-muted-foreground">
                            Untuk
                            program: {{ program.name }}</p>
                    </div>
                    <div
                        class="flex w-full items-center gap-2 sm:w-auto">
                        <Input v-model="search" type="text"
                            placeholder="Cari Order ID / Nama..."
                            class="w-full sm:w-64" />
                        <Select v-model="status">
                            <SelectTrigger
                                class="w-[180px]">
                                <SelectValue
                                    placeholder="Semua Status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="null">
                                    Semua Status
                                </SelectItem>
                                <SelectItem value="paid">
                                    Paid
                                </SelectItem>
                                <SelectItem value="pending">
                                    Pending</SelectItem>
                                <SelectItem value="expired">
                                    Expired</SelectItem>
                                <SelectItem value="failed">
                                    Failed</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <div
                    class="overflow-hidden rounded-lg border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Order ID
                                </TableHead>
                                <TableHead>Nama Donatur
                                </TableHead>
                                <TableHead>Tanggal Transaksi
                                </TableHead>
                                <TableHead>Status
                                </TableHead>
                                <TableHead
                                    class="text-right">
                                    Nominal</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-if="donations.data.length === 0">
                                <TableCell colspan="5"
                                    class="h-24 text-center">
                                    Tidak ada data donasi
                                    ditemukan.</TableCell>
                            </TableRow>
                            <TableRow v-else
                                v-for="donation in donations.data"
                                :key="donation.id">
                                <TableCell
                                    class="font-mono text-sm">
                                    {{
                                        donation.order_id }}
                                </TableCell>
                                <TableCell
                                    class="font-medium">
                                    {{ donation.donator_name
                                    }}
                                </TableCell>
                                <TableCell>{{
                                    formatDateTime(donation.created_at)
                                    }}</TableCell>
                                <TableCell>
                                    <Badge
                                        :class="statusBadgeClass(donation.status)"
                                        class="capitalize">
                                        {{
                                            donation.status }}
                                    </Badge>
                                </TableCell>
                                <TableCell
                                    class="text-right font-semibold">
                                    {{
                                        formatRupiah(donation.amount)
                                    }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
                <Pagination :links="donations.links" />
            </div>
        </div>
    </AppLayout>
</template>