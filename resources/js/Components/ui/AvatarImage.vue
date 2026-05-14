<script setup lang="ts">
import { computed } from "vue";

const props = withDefaults(
    defineProps<{
        src?: string | null;
        alt?: string;
        class?: string;
    }>(),
    {
        src: null,
        alt: "",
        class: "",
    }
);

const classes = computed(() => {
    const base = "aspect-square size-full object-cover";
    return [base, props.class].filter(Boolean).join(" ");
});
</script>

<template>
    <img
        v-if="src"
        data-slot="avatar-image"
        :src="src"
        :alt="alt"
        :class="classes"
    />
</template>
