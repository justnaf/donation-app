<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import { ArrowLeft } from 'lucide-vue-next';

// Tipe Data
interface Program { id: number; slug: string; name: string; }
interface Disbursement { id: number; amount: number; description: string; disbursed_at: string; }

// Props
const props = defineProps<{
    program: Program;
    disbursement?: Disbursement | null;
}>();

const isEditMode = !!props.disbursement;

// Fungsi helper untuk memformat tanggal ke YYYY-MM-DD
const formatDateForInput = (dateString: string | null): string => {
    if (!dateString) return new Date().toISOString().split('T')[0]; // Default ke hari ini
    return new Date(dateString).toISOString().split('T')[0];
};

// Inisialisasi form
const form = useForm({
    amount: props.disbursement?.amount ?? 0,
    description: props.disbursement?.description ?? '',
    disbursed_at: formatDateForInput(props.disbursement?.disbursed_at ?? null),
});

const submit = () => {
    if (isEditMode) {
        form.put(route('admin.disbursements.update', props.disbursement!.id));
    } else {
        form.post(route('admin.disbursements.store', props.program.slug));
    }
};
</script>

<template>
    <form @submit.prevent="submit"
        class="max-w-3xl mx-auto rounded-lg border p-6 bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold">
                    <template v-if="isEditMode">Edit
                        Pencairan Dana</template>
                    <template v-else>Catat Pencairan Dana
                        Baru</template>
                </h1>
                <p class="text-sm text-muted-foreground">
                    Untuk program: {{
                        program.name }}</p>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <Label for="amount" class="mb-2">Jumlah
                    Dicairkan
                    (Rp)</Label>
                <Input id="amount" v-model="form.amount"
                    type="number"
                    placeholder="Contoh: 5000000" />
                <InputError :message="form.errors.amount"
                    class="mt-2" />
            </div>
            <div>
                <Label for="description"
                    class="mb-2">Deskripsi
                    Penggunaan
                    Dana</Label>
                <Textarea id="description"
                    v-model="form.description"
                    placeholder="Contoh: Pembelian 100 paket sembako tahap pertama" />
                <InputError
                    :message="form.errors.description"
                    class="mt-2" />
            </div>
            <div>
                <Label for="disbursed_at"
                    class="mb-2">Tanggal
                    Pencairan</Label>
                <Input id="disbursed_at"
                    v-model="form.disbursed_at"
                    type="date" />
                <InputError
                    :message="form.errors.disbursed_at"
                    class="mt-2" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <Link
                :href="route('admin.disbursements.index')">
            <Button type="button" variant="outline">
                <ArrowLeft class="mr-2 h-4 w-4" /> Kembali
            </Button>
            </Link>
            <Button type="submit"
                :disabled="form.processing">
                <template v-if="isEditMode">Simpan
                    Perubahan</template>
                <template v-else>Catat Pencairan</template>
            </Button>
        </div>
    </form>
</template>
