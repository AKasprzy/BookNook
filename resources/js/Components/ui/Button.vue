<script setup lang="ts">
defineProps<{
    variant?: string
    size?: string
}>()
</script>

<template>
    <button
        v-bind="$attrs"
        class="inline-flex items-center justify-center rounded-md text-sm font-medium transition"
    >
        <slot />
    </button>
</template>
