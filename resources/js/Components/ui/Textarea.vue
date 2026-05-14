<script setup lang="ts">
import { cn } from '@/lib/utils'

const props = defineProps<{
    class?: string
    placeholder?: string
    disabled?: boolean
    rows?: number | string
}>()

const modelValue = defineModel<string | number>()
</script>

<template>
  <textarea
      v-model="modelValue"
      :class="
      cn(
        'resize-none border-input placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 aria-invalid:border-destructive flex min-h-16 w-full rounded-md border bg-transparent px-3 py-2 text-base transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
        props.class,
      )
    "
      :placeholder="placeholder"
      :disabled="disabled"
      :rows="rows"
  />
</template>
