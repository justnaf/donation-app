<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Home, Boxes, User, LogIn, LogOut } from 'lucide-vue-next';
import type { NavItem } from '@/types';
import Footer from '@/components/Footer.vue';
import AppLogo from '@/components/AppLogo.vue';

// Cek apakah user sudah login (reaktif)
const user = computed(() => usePage().props.auth.user);

const dashboardRoute = computed(() => {
    if (!user.value) return route('login');

    // Optional chaining untuk keamanan jika roles null/undefined
    const isAdmin = user.value.roles?.some((role: { name: string }) => role.name === 'admin');

    return isAdmin ? route('admin.dashboard') : route('home');
});

// PERBAIKAN: Ubah navItems menjadi computed agar reaktif terhadap perubahan user
const navItems = computed<NavItem[]>(() => {
    // Item dasar yang selalu ada
    const items = [
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
            activePattern: 'programs.*',
        },
    ];

    // Logika Kondisional: Jika user ada, tampilkan 'Akun Saya', jika tidak 'Akun'
    if (user.value) {
        items.push({
            title: 'Akun Saya',
            href: dashboardRoute.value, // Lebih baik arahkan ke logika dashboardRoute
            icon: User,
            activePattern: 'dashboard', // Sesuaikan jika perlu
        });
    } else {
        items.push({
            title: 'Akun',
            href: route('login'),
            icon: LogIn,
            activePattern: 'login',
        });
    }

    return items;
});
</script>

<template>
    <div class="flex min-h-screen flex-col bg-gray-50">
        <!-- HEADER -->
        <header
            class="fixed top-0 z-40 w-full border-b bg-white/80 backdrop-blur-sm">
            <div
                class="container mx-auto flex h-16 items-center justify-between px-4">
                <Link :href="route('home')"
                    class="flex items-center gap-2">
                    <AppLogo />
                </Link>

                <!-- Navigasi Desktop -->
                <nav
                    class="hidden items-center space-x-6 md:flex">
                    <Link v-for="item in navItems"
                        :key="item.title" :href="item.href"
                        class="text-sm font-medium text-gray-600 transition-colors hover:text-[#F08519]"
                        :class="{ 'text-[#F08519] font-bold': !item.external && item.activePattern ? route().current(item.activePattern) : false }">
                        {{ item.title }}
                    </Link>
                </nav>

                <!-- Tombol Aksi Desktop -->
                <div class="hidden md:block">
                    <Link v-if="user" :href="dashboardRoute"
                        class="rounded-md bg-[#1C1C1C] px-4 py-2 text-sm font-medium text-white transition hover:bg-black mr-3">
                        Dashboard
                    </Link>
                    <Link v-if="user"
                        :href="route('logout')"
                        method="post" as="button"
                        type="button"
                        class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white transition  hover:bg-red-400 ml-3">
                        Keluar
                    </Link>
                    <Link v-else :href="route('login')"
                        class="rounded-md bg-[#F08519] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#F08519]/90">
                        Masuk / Daftar
                    </Link>
                </div>
            </div>
        </header>

        <!-- KONTEN UTAMA HALAMAN -->
        <main class="flex-grow pt-16 pb-20 md:pb-0">
            <slot />
        </main>

        <!-- FOOTER -->
        <Footer />

        <!-- BOTTOM NAVBAR MOBILE -->
        <footer
            class="fixed bottom-0 z-40 block w-full border-t bg-white md:hidden">
            <nav class="grid h-16 grid-cols-3">
                <Link v-for="item in navItems"
                    :key="item.title" :href="item.href"
                    class="flex flex-col items-center justify-center gap-1 text-xs text-gray-500 transition-colors hover:text-[#F08519]"
                    :class="{ 'text-[#F08519] font-semibold': !item.external && item.activePattern ? route().current(item.activePattern) : false }">
                    <component :is="item.icon"
                        class="h-5 w-5" />
                    <span>{{ item.title }}</span>
                </Link>
            </nav>
        </footer>
    </div>
</template>