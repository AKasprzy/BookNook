<script>
import { h } from 'vue'

function createComponent(baseTag, baseClasses, slotName) {
    return {
        name: slotName,
        props: {
            class: { type: String, default: '' }
        },
        setup(props, { slots }) {
            return () =>
                h(
                    baseTag,
                    {
                        'data-slot': slotName,
                        class: [baseClasses, props.class]
                    },
                    slots.default ? slots.default() : []
                )
        }
    }
}

const Card = createComponent(
    'div',
    'rounded-xl border bg-card text-card-foreground shadow',
    'card'
)

const CardHeader = createComponent(
    'div',
    'flex flex-col space-y-1.5 p-6',
    'card-header'
)

const CardTitle = createComponent(
    'h3',
    'text-2xl font-semibold leading-none tracking-tight',
    'card-title'
)

const CardDescription = createComponent(
    'p',
    'text-sm text-muted-foreground',
    'card-description'
)

const CardContent = createComponent(
    'div',
    'p-6 pt-0',
    'card-content'
)

const CardFooter = createComponent(
    'div',
    'flex items-center p-6 pt-0',
    'card-footer'
)

const CardAction = createComponent(
    'div',
    'ml-auto',
    'card-action'
)

export default Card

export {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
    CardFooter,
    CardAction
}
</script>
