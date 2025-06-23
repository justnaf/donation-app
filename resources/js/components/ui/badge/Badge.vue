<script setup lang="ts">
import { type HTMLAttributes } from 'vue'
import { type BadgeVariants, badgeVariants } from './badgeVariants'
import { cn } from '@/lib/utils' // Asumsi Anda punya helper `cn`

interface BadgeProps {
    variant?: BadgeVariants['variant']
    class?: HTMLAttributes['class']
}

const props = defineProps<BadgeProps>()
</script>

<template>
    <div
        :class="cn(badgeVariants({ variant }), props.class)">
        <slot />
    </div>
</template>