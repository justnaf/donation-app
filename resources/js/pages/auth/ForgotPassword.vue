<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { computed, onUnmounted, ref, watch } from 'vue';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const countdown = ref(0);
const timerId = ref<number | null>(null);
const isCountdownActive = ref(false);

const formattedCountdown = computed(() => {
    if (countdown.value <= 0) return '00:00';
    const minutes = Math.floor(countdown.value / 60);
    const seconds = countdown.value % 60;
    return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
});

const startCountdown = (errorMessage: string) => {
    const matches = errorMessage.match(/(\d+)/);
    if (matches && matches[1]) {
        const seconds = parseInt(matches[1], 10);

        countdown.value = seconds;
        isCountdownActive.value = true;

        if (timerId.value) clearInterval(timerId.value);

        timerId.value = window.setInterval(() => {
            countdown.value -= 1;
        }, 1000);

        // Tambahkan watch untuk memonitor kapan countdown selesai
        watch(countdown, (newValue) => {
            if (newValue <= 0) {
                if (timerId.value) clearInterval(timerId.value);
                form.clearErrors('email');
                isCountdownActive.value = false;
            }
        });
    } else {
    }
};

watch(
    () => form.errors.email,
    (errorMessage) => {
        if (isCountdownActive.value) {
            return;
        }

        if (
            errorMessage &&
            (errorMessage.includes('Terlalu banyak') ||
                errorMessage.includes('Too many requests') ||
                errorMessage.includes('Too many login attempts'))
        ) {
            startCountdown(errorMessage);
        } else {
        }
    },
);

onUnmounted(() => {
    if (timerId.value) {
        clearInterval(timerId.value);
    }
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout title="Forgot password" description="Enter your email to receive a password reset link">
        <Head title="Forgot password" />

        <div v-if="status && countdown <= 0" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="space-y-6">
            <form @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" name="email" autocomplete="off" v-model="form.email" autofocus placeholder="email@example.com" />

                    <div v-if="countdown > 0" class="mt-2 text-sm text-destructive">
                        Anda terlalu sering mencoba. Silakan coba lagi dalam {{ formattedCountdown }}
                    </div>
                    <InputError v-else :message="form.errors.email" />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button class="w-full" :disabled="form.processing || countdown > 0">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Email password reset link
                    </Button>
                </div>
            </form>

            <div class="space-x-1 text-center text-sm text-muted-foreground">
                <span>Or, return to</span>
                <TextLink :href="route('login')">log in</TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
