<script setup lang="ts">
import { computed } from "vue";

const props = withDefaults(
    defineProps<{
        class?: string;
    }>(),
    {
        class: "",
    }
);

const classes = computed(() => {
    const base = "bg-muted flex size-full items-center justify-center rounded-full";
    return [base, props.class].filter(Boolean).join(" ");
});
</script>

<template>
    <div data-slot="avatar-fallback" :class="classes">
        <slot />
    </div>
</template>
