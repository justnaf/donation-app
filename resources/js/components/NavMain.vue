<script setup lang="ts">
import { SidebarGroup, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarGroupLabel } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

defineProps<{
    items: NavItem[];
}>();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items"
                :key="item.title">
                <SidebarMenuButton as-child
                    :is-active="!item.external && item.activePattern ? route().current(item.activePattern) : false"
                    :tooltip="item.title">
                    <component
                        :is="item.external ? 'a' : Link"
                        :href="item.href"
                        :target="item.external ? '_blank' : undefined"
                        :rel="item.external ? 'noopener noreferrer' : undefined">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </component>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>