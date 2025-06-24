<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import { ArrowLeft } from 'lucide-vue-next';

// Tipe Data
interface Program { id: number; name: string; slug: string; }
interface Update { id: number; title: string; content: string; }

// Props
const props = defineProps<{
    program: Program; // Program induk selalu diperlukan
    update?: Update | null; // Update bersifat opsional (hanya untuk mode edit)
}>();

const isEditMode = !!props.update;

const form = useForm({
    title: props.update?.title ?? '',
    content: props.update?.content ?? '',
});

const submit = () => {
    if (isEditMode) {
        form.put(route('admin.news.update', props.update!.id));
    } else {
        form.post(route('admin.news.store', props.program.slug));
    }
};
</script>

<template>
    <form @submit.prevent="submit"
        class="max-w-3xl mx-auto rounded-lg border p-6 bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold">
                    <template v-if="isEditMode">Edit Kabar
                        Terbaru</template>
                    <template v-else>Tambah Kabar
                        Baru</template>
                </h1>
                <p class="text-sm text-muted-foreground">
                    Untuk program: {{
                        program.name }}</p>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <Label for="title" class="mb-2">Judul
                    Kabar</Label>
                <Input id="title" v-model="form.title"
                    type="text"
                    placeholder="Contoh: Penyaluran Bantuan Tahap Pertama" />
                <InputError :message="form.errors.title"
                    class="mt-2" />
            </div>
            <div>
                <Label for="content" class="mb-2">Isi
                    Kabar</Label>
                <Textarea id="content"
                    v-model="form.content"
                    placeholder="Ceritakan perkembangan terbaru dari program ini..."
                    class="min-h-[200px]" />
                <InputError :message="form.errors.content"
                    class="mt-2" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <Link
                :href="route('admin.news.index', { program: program.slug })">
            <Button type="button" variant="outline">
                <ArrowLeft class="mr-2 h-4 w-4" /> Kembali
            </Button>
            </Link>
            <Button type="submit"
                :disabled="form.processing">
                <template v-if="isEditMode">Simpan
                    Perubahan</template>
                <template v-else>Publikasikan</template>
            </Button>
        </div>
    </form>
</template>