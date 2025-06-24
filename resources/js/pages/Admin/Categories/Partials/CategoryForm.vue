<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import { ArrowLeft } from 'lucide-vue-next';

interface Category {
    id: number;
    slug: string;
    name: string;
    description: string | null;
}

const props = defineProps<{
    category?: Category | null;
}>();

const isEditMode = !!props.category;

const form = useForm({
    name: props.category?.name ?? '',
    description: props.category?.description ?? '',
});

const submit = () => {
    if (isEditMode) {
        form.put(route('admin.donation-categories.update', props.category!.slug));
    } else {
        form.post(route('admin.donation-categories.store'));
    }
};
</script>

<template>
    <form @submit.prevent="submit"
        class="max-w-2xl mx-auto rounded-lg border p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold">
                    <template v-if="isEditMode">Edit
                        Kategori Donasi</template>
                    <template v-else>Buat Kategori
                        Baru</template>
                </h1>
                <p class="text-sm text-muted-foreground">
                    <template v-if="isEditMode">Perbarui
                        detail kategori di bawah
                        ini.</template>
                    <template v-else>Buat kategori baru
                        untuk mengelompokkan program
                        donasi.</template>
                </p>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <Label for="name" class="mb-2">Nama
                    Kategori</Label>
                <Input id="name" v-model="form.name"
                    type="text"
                    placeholder="Contoh: Pendidikan" />
                <InputError :message="form.errors.name"
                    class="mt-2" />
            </div>
            <div>
                <Label for="description"
                    class="mb-2">Deskripsi
                    (Opsional)</Label>
                <Textarea id="description"
                    v-model="form.description"
                    placeholder="Jelaskan sedikit tentang kategori ini..." />
                <InputError
                    :message="form.errors.description"
                    class="mt-2" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <Link
                :href="route('admin.donation-categories.index')">
            <Button type="button" variant="outline">
                <ArrowLeft class="mr-2 h-4 w-4" /> Batal
            </Button>
            </Link>
            <Button type="submit"
                :disabled="form.processing">
                <template v-if="isEditMode">Simpan
                    Perubahan</template>
                <template v-else>Simpan Kategori</template>
            </Button>
        </div>
    </form>
</template>