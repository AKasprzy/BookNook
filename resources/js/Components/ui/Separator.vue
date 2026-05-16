<script setup lang="ts">
import { computed } from "vue";

const props = withDefaults(
    defineProps<{
        orientation?: "horizontal" | "vertical";
        decorative?: boolean;
        class?: string;
    }>(),
    {
        orientation: "horizontal",
        decorative: true,
        class: "",
    }
);

const classes = computed(() => {
    const base =
        "bg-border shrink-0 " +
        "data-[orientation=horizontal]:h-px data-[orientation=horizontal]:w-full " +
        "data-[orientation=vertical]:h-full data-[orientation=vertical]:w-px";

    return [base, props.class].filter(Boolean).join(" ");
});
</script>

<template>
    <div
        data-slot="separator-root"
        :data-orientation="orientation"
        :aria-hidden="decorative ? 'true' : undefined"
        :role="decorative ? undefined : 'separator'"
        :class="classes"
    />
</template>
