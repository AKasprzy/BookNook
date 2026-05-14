<script setup>
import { computed } from "vue"
import { cva } from "class-variance-authority"

const badgeVariants = cva(
    "inline-flex items-center justify-center rounded-md border px-2 py-0.5 text-xs font-medium w-fit whitespace-nowrap shrink-0 [&>svg]:size-3 gap-1 [&>svg]:pointer-events-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive transition-[color,box-shadow] overflow-hidden",
    {
        variants: {
            variant: {
                default: "border-transparent bg-primary text-primary-foreground hover:bg-primary/90",
                secondary: "border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/90",
                destructive: "border-transparent bg-destructive text-white hover:bg-destructive/90",
                outline: "text-foreground hover:bg-accent hover:text-accent-foreground"
            }
        },
        defaultVariants: {
            variant: "default"
        }
    }
)

const props = defineProps({
    variant: { type: String, default: "default" },
    asChild: { type: Boolean, default: false },
    class: { type: String, default: "" }
})

const badgeClass = computed(() => {
    return [badgeVariants({ variant: props.variant }), props.class]
})
</script>

<template>
    <component
        v-if="!asChild"
        is="span"
        data-slot="badge"
        :class="badgeClass"
    >
        <slot/>
    </component>

    <slot v-else/>
</template>
