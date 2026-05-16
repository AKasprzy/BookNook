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
    const base = "relative flex size-10 shrink-0 overflow-hidden rounded-full";
    return [base, props.class].filter(Boolean).join(" ");
});
</script>

<template>
    <div data-slot="avatar" :class="classes">
        <slot />
    </div>
</template>
