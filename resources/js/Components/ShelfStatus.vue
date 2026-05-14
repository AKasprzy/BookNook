<script setup lang="ts">
import { ref } from 'vue'
import Card from '@/Components/ui/Card.vue'
import Button from '@/Components/ui/Button.vue'
import Badge from '@/Components/ui/Badge.vue'
import { Heart, BookOpen, BookMarked, BookX, CheckCircle2 } from 'lucide-vue-next'
import ReviewDialog from '@/Components/ReviewDialog.vue'

type Status = 'read' | 'reading' | 'tbr' | 'dnf'

interface Shelf {
    id: number
    user_id: number
    book_id: number
    status: Status
    times_read: number
    favourite: boolean
    notes?: string | null
}

interface Props {
    initialShelf?: Shelf
    bookId: number
    onUpdate?: (shelf: Shelf) => void
    onReviewSubmit?: (review: any) => void
}

const props = defineProps<Props>()

const shelf = ref<Shelf | null>(props.initialShelf ?? null)
const showReviewDialog = ref(false)

const statuses = [
    { value: 'read' as Status, label: 'Read', icon: CheckCircle2, color: 'bg-green-500 hover:bg-green-600' },
    { value: 'reading' as Status, label: 'Reading', icon: BookOpen, color: 'bg-blue-500 hover:bg-blue-600' },
    { value: 'tbr' as Status, label: 'To Read', icon: BookMarked, color: 'bg-amber-500 hover:bg-amber-600' },
    { value: 'dnf' as Status, label: 'DNF', icon: BookX, color: 'bg-slate-500 hover:bg-slate-600' },
]

const handleStatusChange = (status: Status) => {
    const newShelf: Shelf = shelf.value
        ? { ...shelf.value, status }
        : {
            id: 0,
            user_id: 0,
            book_id: props.bookId,
            status,
            times_read: 0,
            favourite: false,
            notes: null
        }

    shelf.value = newShelf
    props.onUpdate?.(newShelf)

    if (status === 'read' || status === 'dnf') {
        showReviewDialog.value = true
    }
}

const toggleFavourite = () => {
    if (!shelf.value) return
    shelf.value = { ...shelf.value, favourite: !shelf.value.favourite }
    props.onUpdate?.(shelf.value)
}

const handleReviewSubmit = (review: any) => {
    props.onReviewSubmit?.(review)
}
</script>

<template>
    <Card class="p-6">
        <h3 class="text-slate-900 mb-4">Your Shelf Status</h3>

        <div class="space-y-3">
            <div v-if="shelf && shelf.status" class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                <div class="flex items-center gap-2 mb-3">
                    <component :is="statuses.find(s => s.value === shelf!.status)?.icon" class="w-5 h-5 text-slate-700" />
                    <p class="text-slate-900">
                        {{ statuses.find(s => s.value === shelf!.status)?.label }}
                    </p>
                </div>

                <Button variant="outline" size="sm" @click="shelf = null" class="w-full">
                    Change status
                </Button>
            </div>

            <div v-else class="grid grid-cols-2 gap-2">
                <Button
                    v-for="status in statuses"
                    :key="status.value"
                    :class="status.color + ' text-white gap-2 py-3'"
                    size="sm"
                    @click="handleStatusChange(status.value)"
                >
                    <component :is="status.icon" class="w-4 h-4" />
                    <span class="text-xs">{{ status.label }}</span>
                </Button>
            </div>

            <Button
                v-if="shelf"
                :variant="shelf.favourite ? 'default' : 'outline'"
                @click="toggleFavourite"
                class="w-full gap-2"
            >
                <Heart class="w-4 h-4" />
                {{ shelf.favourite ? 'Favourite' : 'Add to favourites' }}
            </Button>

            <div v-if="shelf && shelf.times_read > 0" class="flex items-center justify-between pt-2 border-t">
                <span class="text-sm text-slate-600">Times Read:</span>
                <Badge variant="secondary">{{ shelf.times_read }}</Badge>
            </div>
        </div>
    </Card>

    <ReviewDialog
        :open="showReviewDialog"
        :edition-id="props.bookId"
        @update:open="val => showReviewDialog = val"
        @submit="handleReviewSubmit"
    />
</template>
