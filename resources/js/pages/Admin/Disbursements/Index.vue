<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Pencil, PlusCircle, Trash2 } from 'lucide-vue-next';
import useSweetAlert from '@/composables/useSweetAlert';

// --- Tipe Data ---
interface Program { id: number; name: string; slug: string; }
interface Disbursement { id: number; description: string; amount: number; disbursed_at: string; }
interface PaginationLink { url: string | null; label: string; active: boolean; }

// --- Props ---
const props = defineProps<{
    allPrograms: Program[];
    disbursements: {
        data: Disbursement[];
        links: PaginationLink[];
    } | null;
    filters: {
        program: string | null;
    };
}>();

// --- State ---
const selectedProgram = ref<string | undefined>(props.filters.program ?? undefined);

const { confirmDelete } = useSweetAlert();

// --- Watcher untuk mengubah data saat dropdown diganti ---
watch(selectedProgram, (newSlug) => {
    // Arahkan ke rute yang sama dengan parameter query baru
    router.get(
        route('admin.disbursements.index'), // Sesuaikan dengan nama rute yang benar
        { program: newSlug },
        { preserveState: true, replace: true, preserveScroll: true }
    );
});

// --- Fungsi Aksi ---
const handleDelete = (disbursementId: number) => {
    confirmDelete(() => {
        router.delete(route('admin.disbursements.destroy', disbursementId), {
            preserveScroll: true,
        });
    });
};

const formatRupiah = (amount: number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
const formatDate = (date: string) => new Date(date).toLocaleDateString('id-ID', { dateStyle: 'long' });

// --- Breadcrumbs ---
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Kelola Pencairan Dana', href: route('admin.disbursements.index') }, // Sesuaikan dengan nama rute
];
</script>

<template>

    <Head title="Kelola Pencairan Dana" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-col gap-6">
                <!-- Header: Dropdown & Tombol Tambah -->
                <div
                    class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <h1 class="text-2xl font-semibold">
                        Kelola
                        Pencairan Dana</h1>
                    <div
                        class="flex w-full items-center gap-2 sm:w-auto">
                        <Select v-model="selectedProgram">
                            <SelectTrigger
                                class="w-full sm:w-[280px]">
                                <SelectValue
                                    placeholder="Pilih program untuk dilihat..." />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="program in allPrograms"
                                    :key="program.id"
                                    :value="program.slug">
                                    {{ program.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>

                        <Link
                            :href="selectedProgram ? route('admin.disbursements.create', { program: selectedProgram }) : '#'"
                            :class="{ 'pointer-events-none opacity-50': !selectedProgram }">
                        <Button
                            :disabled="!selectedProgram">
                            <PlusCircle
                                class="mr-2 h-4 w-4" />
                            Catat Pencairan
                        </Button>
                        </Link>
                    </div>
                </div>

                <!-- Tampilan Awal: Pesan jika belum ada program dipilih -->
                <div v-if="!selectedProgram"
                    class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed h-64 text-center">
                    <p
                        class="text-lg font-medium text-muted-foreground">
                        Pilih Sebuah Program</p>
                    <p
                        class="text-sm text-muted-foreground">
                        Silakan pilih program dari dropdown
                        di
                        atas untuk melihat atau mencatat
                        pencairan dana.</p>
                </div>

                <!-- Tampilan Konten: Tabel atau pesan jika sudah ada program dipilih -->
                <div v-else
                    class="overflow-hidden rounded-lg border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Deskripsi
                                    Penggunaan
                                </TableHead>
                                <TableHead>Jumlah
                                </TableHead>
                                <TableHead>Tanggal Dicairkan
                                </TableHead>
                                <TableHead
                                    class="w-[120px] text-right">
                                    Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-if="!disbursements || disbursements.data.length === 0">
                                <TableCell colspan="4"
                                    class="h-24 text-center">
                                    Belum ada pencairan dana
                                    untuk program ini.
                                </TableCell>
                            </TableRow>
                            <TableRow v-else
                                v-for="disbursement in disbursements.data"
                                :key="disbursement.id">
                                <TableCell
                                    class="font-medium">
                                    {{
                                        disbursement.description
                                    }}</TableCell>
                                <TableCell
                                    class="font-semibold text-primary">
                                    {{
                                        formatRupiah(disbursement.amount)
                                    }}</TableCell>
                                <TableCell>{{
                                    formatDate(disbursement.disbursed_at)
                                }}</TableCell>
                                <TableCell
                                    class="text-right space-x-2">
                                    <Link
                                        :href="route('admin.disbursements.edit', disbursement.id)">
                                    <Button size="icon"
                                        variant="outline">
                                        <Pencil
                                            class="h-4 w-4" />
                                    </Button>
                                    </Link>
                                    <Button
                                        @click="handleDelete(disbursement.id)"
                                        size="icon"
                                        variant="destructive">
                                        <Trash2
                                            class="h-4 w-4" />
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <!-- Paginasi (hanya muncul jika ada data) -->
                <div v-if="disbursements"
                    class="flex justify-center">
                    <Pagination
                        :links="disbursements.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
