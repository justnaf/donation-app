<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, User } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';

const localStorageKey = 'registrationFormCache';

const form = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
    avatar: null as File | null,
});

const avatarPreview = ref<string | null>(null);

watch(
    form,
    (newFormState) => {
        const stateToStore = { ...newFormState };
        if ('avatar' in stateToStore) {
            delete (stateToStore as Record<string, unknown>).avatar;
        }

        localStorage.setItem(localStorageKey, JSON.stringify(stateToStore));
    },
    { deep: true },
);

onMounted(() => {
    const cachedData = localStorage.getItem(localStorageKey);
    if (cachedData) {
        const parsedData = JSON.parse(cachedData);
        form.defaults(parsedData);
        form.reset();
    }
});

function handleAvatarChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        const file = target.files[0];
        form.avatar = file;
        avatarPreview.value = URL.createObjectURL(file);
    }
}

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        // --- PERUBAHAN 4: Menghapus cache HANYA jika registrasi berhasil ---
        onSuccess: () => {
            localStorage.removeItem(localStorageKey);
        },
    });
};
</script>

<template>
    <AuthBase title="Create an account"
        description="Enter your details below to create your account">

        <Head title="Register" />

        <form @submit.prevent="submit"
            class="flex flex-col gap-6">
            <div class="flex flex-col items-center gap-2">
                <Label for="avatar-upload"
                    class="cursor-pointer">
                    <div
                        class="relative h-24 w-24 rounded-full">
                        <img v-if="avatarPreview"
                            :src="avatarPreview"
                            alt="Avatar Preview"
                            class="h-full w-full rounded-full object-cover" />
                        <div v-else
                            class="flex h-full w-full items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-800">
                            <User
                                class="h-12 w-12 text-neutral-400" />
                        </div>
                        <div
                            class="absolute inset-0 flex items-center justify-center rounded-full bg-black/40 opacity-0 transition-opacity hover:opacity-100">
                            <span
                                class="text-xs font-semibold text-white">Change</span>
                        </div>
                    </div>
                </Label>
                <p class="text-xs"><span
                        class="text-red-500"> *</span>Jangan
                    lupa upload foto avatar</p>
                <input id="avatar-upload" type="file"
                    class="hidden"
                    @change="handleAvatarChange"
                    accept="image/*" />
                <InputError :message="form.errors.avatar" />
            </div>
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" type="text" required
                        autofocus :tabindex="1"
                        autocomplete="name"
                        v-model="form.name"
                        placeholder="Full name" />
                    <InputError
                        :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="username">Username</Label>
                    <Input id="username" type="text"
                        required :tabindex="2"
                        autocomplete="username"
                        v-model="form.username"
                        placeholder="your_unique_username" />
                    <InputError
                        :message="form.errors.username" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" required
                        :tabindex="3" autocomplete="email"
                        v-model="form.email"
                        placeholder="email@example.com" />
                    <InputError
                        :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input id="password" type="password"
                        required :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Password" />
                    <InputError
                        :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label
                        for="password_confirmation">Confirm
                        password</Label>
                    <Input id="password_confirmation"
                        type="password" required
                        :tabindex="5"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Confirm password" />
                    <InputError
                        :message="form.errors.password_confirmation" />
                </div>

                <Button type="submit"
                    class="mt-2 w-full bg-[#F08519] text-white hover:bg-[#F08519]/90"
                    :tabindex="6"
                    :disabled="form.processing">
                    <LoaderCircle v-if="form.processing"
                        class="h-4 w-4 animate-spin" />
                    Create account
                </Button>
            </div>

            <div
                class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')"
                    class="underline underline-offset-4"
                    :tabindex="7">Log in</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
