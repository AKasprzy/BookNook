<template>
    <Card v-if="book?.id" class="p-6">
        <CardTitle class="mb-6">Original Book Info</CardTitle>
        <div class="space-y-4">
            <template v-for="(detail, index) in detailsArray" :key="index">
                <div v-if="detail.value" class="flex items-start gap-3">
                    <component :is="detail.icon" class="w-5 h-5 text-slate-500 mt-0.5 flex-shrink-0" />
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-slate-500">{{ detail.label }}</p>
                        <p class="text-slate-900 break-words">{{ detail.value }}</p>
                    </div>
                </div>
                <Separator v-if="detail.value && index < detailsArray.filter(d => d.value).length - 1" class="mt-4" />
            </template>
        </div>
    </Card>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Card, CardTitle } from './ui/Card.vue'
import Separator from './ui/Separator.vue'
import { BookMarked, Calendar, Languages, List } from 'lucide-vue-next'

interface Book {
    id: number
    title: string
    original_language: string
    original_publication_date: string | null
    series: string | null
    author: string
}

const props = defineProps<{ book: Book }>()

const detailsArray = computed(() => [
    { icon: BookMarked, label: 'Original Title', value: props.book.title },
    { icon: Languages, label: 'Original Language', value: props.book.original_language },
    {
        icon: Calendar,
        label: 'Original Publication',
        value: props.book.original_publication_date
            ? new Date(props.book.original_publication_date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            })
            : null,
    },
    { icon: List, label: 'Series', value: props.book.series },
])
</script>
