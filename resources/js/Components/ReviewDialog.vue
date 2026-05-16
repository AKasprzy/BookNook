<script setup lang="ts">
import { ref, computed } from 'vue'
import axios from 'axios'
import Card from './ui/Card.vue'
import Button from './ui/Button.vue'
import { Heart, CheckCircle2, BookOpen, BookMarked, BookX } from 'lucide-vue-next'
import ReviewDialog from './ReviewDialog.vue'

type Status = 'read' | 'reading' | 'tbr' | 'dnf'

interface Shelf {
    id: number
    user_id: number
    book_id: number
    status: Status
    times_read: number
    favourite: boolean
    notes?: string
}

const props = defineProps<{
    initialShelf?: Shelf
    editionId: number
}>()

const shelf = ref<Shelf | null>(props.initialShelf ?? null)
const showReviewDialog = ref(false)

const statuses: {
    value: Status
    label: string
    icon: any
    color: string
}[] = [
    { value: 'read', label: 'Read', icon: CheckCircle2, color: 'bg-green-500 hover:bg-green-600' },
    { value: 'reading', label: 'Reading', icon: BookOpen, color: 'bg-blue-500 hover:bg-blue-600' },
    { value: 'tbr', label: 'Want to Read', icon: BookMarked, color: 'bg-amber-500 hover:bg-amber-600' },
    { value: 'dnf', label: 'Did Not Finish', icon: BookX, color: 'bg-slate-500 hover:bg-slate-600' },
]

const currentStatus = computed(() =>
    shelf.value ? statuses.find(s => s.value === shelf.value!.status) : undefined
)

const currentIcon = computed(() => currentStatus.value?.icon)
const currentLabel = computed(() => currentStatus.value?.label)

async function updateStatus(status: Status) {
    const { data } = await axios.post('/api/shelves', {
        book_edition_id: props.editionId,
        status
    })

    shelf.value = data

    if (status === 'read' || status === 'dnf') {
        showReviewDialog.value = true
    }
}

async function toggleFavourite() {
    if (!shelf.value) return

    const { data } = await axios.put(`/api/shelves/${shelf.value.id}`, {
        favourite: !shelf.value.favourite
    })

    shelf.value = data
}

function submitReview(review: { rating: number | null; review_text: string; spoiler: boolean; reread: boolean }) {
    axios.post('/api/reviews', {
        book_edition_id: props.editionId,
        ...review
    })

    showReviewDialog.value = false
}
</script>

<template>
    <Card class="p-6">
        <h3 class="text-slate-900 mb-4">Your Reading Status</h3>

        <div class="space-y-3">
            <div v-if="shelf && shelf.status" class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                <div class="flex items-center gap-2 mb-3">
                    <component :is="currentIcon" class="w-5 h-5 text-slate-700" />
                    <p class="text-slate-900">{{ currentLabel }}</p>
                </div>

                <Button variant="outline" size="sm" @click="shelf = null" class="w-full">
                    Change Status
                </Button>
            </div>

            <div v-else class="grid grid-cols-2 gap-2">
                <Button
                    v-for="status in statuses"
                    :key="status.value"
                    :class="status.color + ' text-white gap-2 h-auto py-3'"
                    size="sm"
                    @click="updateStatus(status.value)"
                >
                    <component :is="status.icon" class="w-4 h-4" />
                    <span class="text-xs">{{ status.label }}</span>
                </Button>
            </div>

            <Button
                v-if="shelf"
                :variant="shelf.favourite ? 'default' : 'outline'"
                class="w-full gap-2"
                @click="toggleFavourite"
            >
                <Heart :class="['w-4 h-4', shelf.favourite ? 'fill-current' : '']" />
                {{ shelf.favourite ? 'Favourite' : 'Add to Favourites' }}
            </Button>

            <div v-if="shelf && shelf.times_read > 0" class="flex items-center justify-between pt-2 border-t">
                <span class="text-sm text-slate-600">Times Read:</span>
                <span class="bg-slate-200 px-2 py-1 rounded">{{ shelf.times_read }}</span>
            </div>
        </div>
    </Card>

    <ReviewDialog
        :open="showReviewDialog"
        :edition-id="props.editionId"
        @update:open="showReviewDialog = $event"
        @submit="submitReview"
    />
</template>
