<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import { Checkbox } from '@/components/ui/checkbox';

const props = defineProps<{
    programId: number;
}>();

const form = useForm({
    donator_name: '',
    donator_email: '',
    amount: 0,
    donation_date: new Date().toISOString().split('T')[0], // Default ke hari ini
    is_anonymous: false,
    message: '',
});

const submit = () => {
    form.post(route('admin.programs.donations.store', props.programId), {
        onSuccess: () => form.reset(), // Reset form setelah sukses
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="rounded-lg border p-6">
        <h3 class="text-lg font-semibold mb-4">Catat Donasi
            Offline/Tunai</h3>
        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <Label for="donator_name " class="mb-2">Nama
                    Donatur</Label>
                <Input id="donator_name"
                    v-model="form.donator_name"
                    type="text" />
                <InputError
                    :message="form.errors.donator_name"
                    class="mt-1" />
            </div>
            <div>
                <Label for="donator_email"
                    class="mb-2">Email
                    Donatur</Label>
                <Input id="donator_email"
                    v-model="form.donator_email"
                    type="email" />
                <InputError
                    :message="form.errors.donator_email"
                    class="mt-1" />
            </div>
            <div>
                <Label for="amount" class="mb-2">Jumlah
                    (Rp)</Label>
                <Input id="amount" v-model="form.amount"
                    type="number" />
                <InputError :message="form.errors.amount"
                    class="mt-1" />
            </div>
            <div>
                <Label for="donation_date"
                    class="mb-2">Tanggal Donasi
                    Diterima</Label>
                <Input id="donation_date"
                    v-model="form.donation_date"
                    type="date" />
                <InputError
                    :message="form.errors.donation_date"
                    class="mt-1" />
            </div>
            <div>
                <Label for="message" class="mb-2">Pesan/Doa
                    (Opsional)</Label>
                <Textarea id="message"
                    v-model="form.message" />
                <InputError :message="form.errors.message"
                    class="mt-1" />
            </div>
            <div class="flex items-center space-x-2">
                <Checkbox id="is_anonymous"
                    v-model:checked="form.is_anonymous" />
                <Label for="is_anonymous">Sembunyikan nama
                    (Donasi Anonim)</Label>
            </div>
            <div class="text-right">
                <Button :disabled="form.processing">Simpan
                    Donasi</Button>
            </div>
        </form>
    </div>
</template>