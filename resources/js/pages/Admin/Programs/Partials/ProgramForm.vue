<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

// Impor komponen UI dan ikon
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Image as ImageIcon, ArrowLeft } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';

// Definisikan tipe data
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
    short_description: string;
    content: string;
}

// 1. Terima 'program' sebagai prop, bisa null (untuk mode create)
const props = defineProps<{
    program?: Program | null;
}>();

// 2. Tentukan apakah kita dalam mode 'edit'
const isEditMode = !!props.program;

// 3. Inisialisasi form secara dinamis
const form = useForm({
    _method: isEditMode ? 'PUT' : 'POST', // Trik untuk upload file di form update
    name: props.program?.name ?? '',
    poster: null as File | null,
    target_amount: props.program?.target_amount ?? 0,
    start_date: props.program?.start_date?.split('T')[0] ?? '', // Ambil format YYYY-MM-DD
    end_date: props.program?.end_date?.split('T')[0] ?? '',
    short_description: props.program?.short_description ?? '',
    content: props.program?.content ?? '',
    status: props.program?.status ?? 'draft',
});

// 4. Atur preview poster secara dinamis
const posterPreview = ref<string | null>(props.program?.poster_url ?? null);

const handlePosterChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        form.poster = file;
        posterPreview.value = URL.createObjectURL(file);
    }
};

// 5. Buat fungsi submit yang dinamis
const submit = () => {
    if (isEditMode) {
        // PENTING: Untuk update dengan file, kita harus menggunakan POST,
        // tapi dengan menyertakan _method: 'PUT'. Laravel akan mengerti.
        form.post(route('admin.programs.update', props.program!.slug));
    } else {
        form.post(route('admin.programs.store'));
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">
                    <span v-if="isEditMode">Edit Program
                        Donasi</span>
                    <span v-else>Buat Program Donasi
                        Baru</span>
                </h1>
                <p class="text-sm text-muted-foreground">
                    <span v-if="isEditMode">Perbarui detail
                        program di bawah ini.</span>
                    <span v-else>Isi detail di bawah untuk
                        membuat program donasi baru.</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Link :href="route('admin.programs.index')">
                <Button type="button" variant="outline">
                    <ArrowLeft class="mr-2 h-4 w-4" /> Batal
                </Button>
                </Link>
                <Button type="submit"
                    :disabled="form.processing">
                    {{ isEditMode ? 'Update Program' :
                        'Simpan Program' }}
                </Button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-lg border p-6">
                    <div class="grid gap-4">
                        <div>
                            <Label for="name"
                                class="mb-2">Nama
                                Program</Label>
                            <Input id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Contoh: Bantuan Bencana Alam" />
                            <InputError
                                :message="form.errors.name"
                                class="mt-2" />
                        </div>
                        <div>
                            <Label for="short_description"
                                class="mb-2">Deskripsi
                                Singkat</Label>
                            <Textarea id="short_description"
                                v-model="form.short_description"
                                placeholder="Deskripsi singkat yang akan tampil di kartu program..." />
                            <InputError
                                :message="form.errors.short_description"
                                class="mt-2" />
                        </div>
                        <div>
                            <Label for="content"
                                class="mb-2">Konten
                                / Cerita
                                Lengkap</Label>
                            <Textarea id="content"
                                v-model="form.content"
                                placeholder="Jelaskan secara detail tentang program donasi ini..."
                                class="min-h-[200px]" />
                            <InputError
                                :message="form.errors.content"
                                class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="space-y-6 lg:col-span-1">
                <div class="rounded-lg border p-6">
                    <div class="grid gap-4">
                        <div>
                            <Label>Poster
                                Program</Label>
                            <div v-if="posterPreview"
                                class="my-2">
                                <img :src="posterPreview"
                                    alt="Preview Poster"
                                    class="w-full rounded-md object-cover" />
                            </div>
                            <div v-else
                                class="my-2 flex h-32 w-full items-center justify-center rounded-md border-2 border-dashed">
                                <ImageIcon
                                    class="h-8 w-8 text-gray-400" />
                            </div>
                            <Input id="poster" type="file"
                                @change="handlePosterChange"
                                accept="image/*" />
                            <InputError
                                :message="form.errors.poster"
                                class="mt-2" />
                        </div>
                        <div>
                            <Label for="status"
                                class="mb-2">Status</Label>
                            <Select v-model="form.status">
                                <SelectTrigger>
                                    <SelectValue
                                        placeholder="Pilih status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        value="draft">
                                        Draf
                                    </SelectItem>
                                    <SelectItem
                                        value="active">
                                        Aktif
                                    </SelectItem>
                                    <SelectItem
                                        value="ended">
                                        Ended
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError
                                :message="form.errors.status"
                                class="mt-2" />
                        </div>
                        <div>
                            <Label for="target_amount"
                                class="mb-2">Target
                                Donasi (Rp)</Label>
                            <Input id="target_amount"
                                v-model="form.target_amount"
                                type="number"
                                placeholder="0" />
                            <InputError
                                :message="form.errors.target_amount"
                                class="mt-2" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <Label for="start_date"
                                    class="mb-2">Tanggal
                                    Mulai
                                    (Opsional)</Label>
                                <Input id="start_date"
                                    v-model="form.start_date"
                                    type="date" />
                                <InputError
                                    :message="form.errors.start_date"
                                    class="mt-2" />
                            </div>
                            <div>
                                <Label for="end_date"
                                    class="mb-2">Tanggal
                                    Selesai
                                    (Opsional)</Label>
                                <Input id="end_date"
                                    v-model="form.end_date"
                                    type="date" />
                                <InputError
                                    :message="form.errors.end_date"
                                    class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>