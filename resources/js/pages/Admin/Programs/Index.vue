<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Image as ImageIcon, Pencil, PlusCircle, Trash2, List } from 'lucide-vue-next';
import useSweetAlert from '@/composables/useSweetAlert';
import Pagination from '@/components/Pagination.vue';

type ProgramStatus = 'draft' | 'active' | 'ended';

interface Program {
    id: number;
    name: string;
    slug: string;
    poster_url: string;
    status: ProgramStatus;
    target_amount: number;
    collected_amount: number;
    start_date: string | null;
    end_date: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PageProps {
    programs: {
        data: Program[];
        links: PaginationLink[];
    };
    filters: {
        search: string;
    };
}

const props = defineProps<PageProps>();
const { confirmDelete } = useSweetAlert();


const search = ref(props.filters.search);
let searchTimer: ReturnType<typeof setTimeout>;

watch(search, (value: string) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(route('admin.programs.index'), { search: value }, { preserveState: true, replace: true });
    }, 300); // Debounce 300ms
});

const statusOrder: ProgramStatus[] = ['draft', 'active', 'ended'];

const cycleStatus = (program: Program) => {
    const currentIndex = statusOrder.indexOf(program.status);
    const nextIndex = (currentIndex + 1) % statusOrder.length;
    const newStatus = statusOrder[nextIndex];

    router.patch(
        route('admin.programs.update.status', program.slug),
        { status: newStatus },
        { preserveScroll: true },
    );
};

const handleDelete = (program: Program) => {
    confirmDelete(() => {
        router.delete(route('admin.programs.destroy', program.slug), {
            preserveScroll: true,
        });
    });
};

const statusBadgeClass = (status: ProgramStatus) => {
    return {
        draft: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        active: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        ended: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    }[status];
};

const formatRupiah = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount);
};

const formatDate = (dateString: string | null) => {
    if (!dateString) return 'Tanpa Batas';
    return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Kelola Program Donasi', href: route('admin.programs.index') }];
</script>

<template>

    <Head title="Kelola Program Donasi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-col gap-6">
                <div
                    class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <h2
                        class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                        Daftar Program Donasi</h2>
                    <div
                        class="flex w-full items-center gap-2 sm:w-auto">
                        <input v-model="search" type="text"
                            placeholder="Cari program..."
                            class="block w-full rounded-md border-gray-300 bg-white p-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-64 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:focus:border-indigo-600" />
                        <Link
                            :href="route('admin.programs.create')">
                        <Button>
                            <PlusCircle
                                class="mr-2 h-4 w-4" />
                            Tambah Program
                        </Button>
                        </Link>
                    </div>
                </div>

                <div
                    class="overflow-hidden rounded-lg border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead
                                    class="w-[100px]">Poster
                                </TableHead>
                                <TableHead>Nama Program
                                </TableHead>
                                <TableHead>Status
                                </TableHead>
                                <TableHead>Target &
                                    Terkumpul</TableHead>
                                <TableHead>Periode
                                </TableHead>
                                <TableHead
                                    class="w-[120px] text-right">
                                    Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <template
                                v-if="programs.data.length > 0">
                                <TableRow
                                    v-for="program in programs.data"
                                    :key="program.id">
                                    <TableCell>
                                        <img v-if="program.poster_url && !program.poster_url.includes('placeholder.com')"
                                            :src="program.poster_url"
                                            class="h-12 w-20 rounded-md object-cover"
                                            alt="Poster" />
                                        <div v-else
                                            class="flex h-12 w-20 items-center justify-center rounded-md bg-gray-100 dark:bg-gray-700">
                                            <ImageIcon
                                                class="h-6 w-6 text-gray-400" />
                                        </div>
                                    </TableCell>
                                    <TableCell
                                        class="font-medium">
                                        {{ program.name }}
                                    </TableCell>
                                    <TableCell>
                                        <button
                                            @click="cycleStatus(program)"
                                            type="button"
                                            class="rounded-full px-2.5 py-1 text-xs font-semibold capitalize transition-transform duration-150 hover:scale-105 active:scale-100"
                                            :class="statusBadgeClass(program.status)">
                                            {{
                                                program.status
                                            }}
                                        </button>
                                    </TableCell>
                                    <TableCell>
                                        <div
                                            class="flex flex-col">
                                            <span
                                                class="font-semibold text-green-600">{{
                                                    formatRupiah(program.collected_amount)
                                                }}</span>
                                            <span
                                                class="text-xs text-gray-500">dari
                                                {{
                                                    formatRupiah(program.target_amount)
                                                }}</span>
                                        </div>
                                    </TableCell>
                                    <TableCell
                                        class="whitespace-nowrap">
                                        {{
                                            formatDate(program.start_date)
                                        }}
                                    </TableCell>
                                    <TableCell
                                        class="space-x-2 text-right flex">
                                        <Link
                                            :href="route('admin.programs.donations.index', { program: program.slug })"
                                            title="Lihat Daftar Donasi">
                                        <Button
                                            variant="outline"
                                            size="icon">
                                            <List
                                                class="h-4 w-4" />
                                        </Button>
                                        </Link>
                                        <Link
                                            :href="route('admin.programs.edit', program.slug)">
                                        <Button
                                            variant="outline"
                                            size="icon">
                                            <Pencil
                                                class="h-4 w-4" />
                                        </Button>
                                        </Link>
                                        <Button
                                            @click="handleDelete(program)"
                                            variant="destructive"
                                            size="icon">
                                            <Trash2
                                                class="h-4 w-4" />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </template>
                            <template v-else>
                                <TableRow>
                                    <TableCell colspan="6"
                                        class="h-24 text-center">
                                        Tidak ada data
                                        program ditemukan.
                                    </TableCell>
                                </TableRow>
                            </template>
                        </TableBody>
                    </Table>
                </div>

                <Pagination :links="programs.links" />
            </div>
        </div>
    </AppLayout>
</template>
