<script setup lang="ts">
import { type HTMLAttributes, computed } from 'vue'
import { ProgressIndicator, ProgressRoot, type ProgressRootProps } from 'radix-vue'
import { cn } from '@/lib/utils'

const props = withDefaults(defineProps<ProgressRootProps & { class?: HTMLAttributes['class'] }>(), {
    modelValue: 0,
})

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props
    return delegated
})
</script>

<template>
    <div>
        <ProgressRoot v-bind="delegatedProps"
            :class="cn('relative h-4 w-full overflow-hidden rounded-full bg-secondary', props.class)">
            <ProgressIndicator
                class="h-full w-full flex-1 bg-[#F08519]"
                :style="`transform: translateX(-${100 - (modelValue ?? 0)}%);`" />
        </ProgressRoot>
    </div>
</template>