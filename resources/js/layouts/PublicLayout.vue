<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Home, Boxes, User, LogIn } from 'lucide-vue-next'; // Impor ikon
import type { NavItem } from '@/types'; // Asumsi tipe NavItem sudah ada

// Data untuk item navigasi
const navItems: NavItem[] = [
    {
        title: 'Beranda',
        href: route('home'),
        icon: Home,
        activePattern: 'home',
    },
    {
        title: 'Program',
        href: route('programs.index'),
        icon: Boxes,
        activePattern: 'programs',
    },
    {
        title: 'Akun',
        href: route('login'), // Arahkan ke login jika belum masuk
        icon: LogIn,
        activePattern: 'login',
    },
];

// Cek apakah user sudah login
const user = computed(() => usePage().props.auth.user);

// Jika sudah login, ganti item navigasi 'Akun'
if (user.value) {
    navItems[2] = {
        title: 'Akun Saya',
        href: route('dashboard'), // Arahkan ke dashboard
        icon: User,
        activePattern: 'dashboard',
    };
}
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <header
            class="fixed top-0 z-40 w-full border-b bg-white/80 backdrop-blur-sm">
            <div
                class="container mx-auto flex h-16 items-center justify-between px-4">
                <Link :href="route('home')"
                    class="text-xl font-bold text-gray-800">
                DonasiYuk </Link>

                <nav
                    class="hidden items-center space-x-6 md:flex">
                    <Link v-for="item in navItems"
                        :key="item.title" :href="item.href"
                        class="text-sm font-medium text-gray-600 transition-colors hover:text-primary"
                        :class="{ 'text-primary': route().current(item.activePattern || '') }">
                    {{ item.title }}
                    </Link>
                </nav>

                <div class="hidden md:block">
                    <Link v-if="user"
                        :href="route('dashboard')"
                        class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700">
                    Dashboard
                    </Link>
                    <Link v-else :href="route('login')"
                        class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition hover:bg-primary/90">
                    Masuk / Daftar
                    </Link>
                </div>
            </div>
        </header>

        <main class="pt-16 pb-20 md:pb-0">
            <slot />
        </main>

        <footer
            class="fixed bottom-0 z-40 block w-full border-t bg-white md:hidden">
            <nav class="grid h-16 grid-cols-3">
                <Link v-for="item in navItems"
                    :key="item.title" :href="item.href"
                    class="flex flex-col items-center justify-center gap-1 text-xs text-gray-500 transition-colors hover:text-primary"
                    :class="{ 'text-primary': route().current(item.activePattern || '') }">
                <component :is="item.icon"
                    class="h-5 w-5" />
                <span>{{ item.title }}</span>
                </Link>
            </nav>
        </footer>
    </div>
</template>