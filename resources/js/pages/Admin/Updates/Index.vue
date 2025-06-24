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

// --- Tipe Data ---
interface Program { id: number; name: string; slug: string; }
interface Update { id: number; title: string; created_at: string; }
interface PaginationLink { url: string | null; label: string; active: boolean; }

// --- Props ---
const props = defineProps<{
    allPrograms: Program[];
    updates: {
        data: Update[];
        links: PaginationLink[];
    } | null;
    filters: {
        program: string | null;
    };
}>();

// --- State ---
const selectedProgram = ref<string | undefined>(props.filters.program ?? undefined);

// --- Watcher untuk mengubah data saat dropdown diganti ---
watch(selectedProgram, (newSlug) => {
    if (!newSlug) {
        router.get(route('admin.news.index'));
        return;
    }
    router.get(
        route('admin.news.index'),
        { program: newSlug },
        { preserveState: true, replace: true, preserveScroll: true }
    );
});

// --- Fungsi Aksi ---
const handleDelete = (updateId: number) => {
    if (confirm('Anda yakin ingin menghapus kabar terbaru ini?')) {
        router.delete(route('admin.news.destroy', updateId), { preserveScroll: true });
    }
};
const formatDate = (date: string) => new Date(date).toLocaleDateString('id-ID', { dateStyle: 'long' });

// --- Breadcrumbs ---
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Kelola Kabar Terbaru', href: route('admin.news.index') },
];
</script>

<template>

    <Head title="Kelola Kabar Terbaru" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-col gap-6">
                <!-- Header: Dropdown & Tombol Tambah -->
                <div
                    class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <h1 class="text-2xl font-semibold">
                        Kelola
                        Kabar Terbaru</h1>
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
                            :href="selectedProgram ? route('admin.news.create', { program: selectedProgram }) : '#'"
                            :class="{ 'pointer-events-none opacity-50': !selectedProgram }">
                        <Button
                            :disabled="!selectedProgram">
                            <PlusCircle
                                class="mr-2 h-4 w-4" />
                            Tambah Kabar
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
                        atas untuk melihat atau menambah
                        kabar
                        terbaru.</p>
                </div>

                <!-- Tampilan Konten: Tabel atau pesan jika sudah ada program dipilih -->
                <div v-else
                    class="overflow-hidden rounded-lg border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Judul Kabar
                                </TableHead>
                                <TableHead>Tanggal Publikasi
                                </TableHead>
                                <TableHead
                                    class="w-[120px] text-right">
                                    Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-if="!updates || updates.data.length === 0">
                                <TableCell colspan="3"
                                    class="h-24 text-center">
                                    Belum ada kabar terbaru
                                    untuk program ini.
                                </TableCell>
                            </TableRow>
                            <TableRow v-else
                                v-for="update in updates.data"
                                :key="update.id">
                                <TableCell
                                    class="font-medium">
                                    {{ update.title }}
                                </TableCell>
                                <TableCell>{{
                                    formatDate(update.created_at)
                                    }}</TableCell>
                                <TableCell
                                    class="text-right space-x-2">
                                    <Link
                                        :href="route('admin.news.edit', update.id)">
                                    <Button size="icon"
                                        variant="outline">
                                        <Pencil
                                            class="h-4 w-4" />
                                    </Button>
                                    </Link>
                                    <Button
                                        @click="handleDelete(update.id)"
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

                <!-- Paginasi (hanya muncul jika ada data update) -->
                <div v-if="updates"
                    class="flex justify-center">
                    <Pagination :links="updates.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>