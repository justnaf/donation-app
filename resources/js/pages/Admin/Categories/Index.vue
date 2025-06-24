<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Pencil, PlusCircle, Trash2 } from 'lucide-vue-next';
import useSweetAlert from '@/composables/useSweetAlert';

// Tipe Data
interface Category {
    id: number;
    name: string;
    slug: string;
    programs_count: number; // Jumlah program dalam kategori ini
    created_at: string;
}
interface PaginatedCategories {
    data: Category[];
    links: { url: string | null; label: string; active: boolean; }[];
}

// Props
defineProps<{
    categories: PaginatedCategories;
}>();
const { confirmDelete } = useSweetAlert();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Kelola Kategori', href: route('admin.donation-categories.index') },
];

// Fungsi Hapus
const handleDelete = (category: Category) => {
    confirmDelete(() => {
        router.delete(route('admin.donation-categories.destroy', category.slug), {
            preserveScroll: true,
        });
    });
};

const formatDateTime = (dateString: string) => {
    return new Date(dateString).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' });
};
</script>

<template>

    <Head title="Kelola Kategori Donasi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Kelola
                    Kategori Donasi</h1>
                <Link
                    :href="route('admin.donation-categories.create')">
                <Button>
                    <PlusCircle class="mr-2 h-4 w-4" />
                    Tambah Kategori Baru
                </Button>
                </Link>
            </div>

            <div
                class="mt-4 overflow-hidden rounded-lg border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nama Kategori
                            </TableHead>
                            <TableHead>Slug</TableHead>
                            <TableHead>Jumlah Program
                            </TableHead>
                            <TableHead>Tanggal Dibuat
                            </TableHead>
                            <TableHead
                                class="w-[120px] text-right">
                                Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-if="categories.data.length === 0">
                            <TableCell colspan="5"
                                class="h-24 text-center">
                                Belum
                                ada kategori donasi.
                            </TableCell>
                        </TableRow>
                        <TableRow v-else
                            v-for="category in categories.data"
                            :key="category.id">
                            <TableCell class="font-medium">
                                {{
                                    category.name }}</TableCell>
                            <TableCell
                                class="font-mono text-sm">{{
                                    category.slug }}</TableCell>
                            <TableCell>{{
                                category.programs_count }}
                            </TableCell>
                            <TableCell>{{
                                formatDateTime(category.created_at)
                            }}</TableCell>
                            <TableCell
                                class="space-x-2 text-right">
                                <Link
                                    :href="route('admin.donation-categories.edit', category.slug)">
                                <Button variant="outline"
                                    size="icon"
                                    title="Edit Kategori">
                                    <Pencil
                                        class="h-4 w-4" />
                                </Button>
                                </Link>
                                <Button
                                    @click="handleDelete(category)"
                                    variant="destructive"
                                    size="icon"
                                    title="Hapus Kategori">
                                    <Trash2
                                        class="h-4 w-4" />
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div class="mt-4 flex justify-center">
                <Pagination :links="categories.links" />
            </div>
        </div>
    </AppLayout>
</template>