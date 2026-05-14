<script setup lang="ts">
defineProps({
    modelValue: [String, Number]
})

defineEmits(['update:modelValue'])
</script>

<template>
    <input
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        v-bind="$attrs"
        class="w-full h-10 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 disabled:opacity-50 disabled:cursor-not-allowed"
    />
</template>
