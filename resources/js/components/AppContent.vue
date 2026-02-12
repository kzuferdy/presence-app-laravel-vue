<script setup lang="ts">
import { SidebarInset } from '@/components/ui/sidebar';
import { computed } from 'vue';

interface Props {
    variant?: 'header' | 'sidebar';
    class?: string;
}

const props = defineProps<Props>();
const className = computed(() => props.class);
</script>

<template>
    <SidebarInset
    v-if="props.variant === 'sidebar'"
    :class="className"
>
    <div class="flex flex-1 flex-col px-6 py-6 lg:px-8 max-w-7xl mx-auto w-full">
        <slot />
    </div>
</SidebarInset>

    <main
        v-else
        class="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl"
        :class="className"
    >
        <slot />
    </main>
</template>
