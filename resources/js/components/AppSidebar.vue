<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Boxes, LayoutGrid, BookMarked, Newspaper, HandCoins } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: route('admin.dashboard'),
        icon: LayoutGrid,
        activePattern: 'admin.dashboard',
    },
    {
        title: 'Kelola Kategori',
        href: route('admin.donation-categories.index'),
        icon: BookMarked, // Contoh ikon
        activePattern: 'admin.donation-categories.*',
    },
    {
        title: 'Programs',
        href: route('admin.programs.index'),
        icon: Boxes,
        activePattern: 'admin.programs.*',
    },
    {
        title: 'Programs News',
        href: route('admin.news.index'),
        icon: Newspaper,
        activePattern: 'admin.news.*',
    },
    {
        title: 'Programs Disbursements',
        href: route('admin.disbursements.index'),
        icon: HandCoins,
        activePattern: 'admin.disbursements.*',
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
        external: true,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link
                            :href="route('admin.dashboard')">
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
