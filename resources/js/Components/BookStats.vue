<template>
    <Card class="p-6" v-if="editionId">
        <CardTitle class="mb-4">Community Stats</CardTitle>
        <div class="grid grid-cols-2 gap-4">
            <div v-for="stat in statsArray" :key="stat.label" class="text-center">
                <div class="flex items-center justify-center mb-2">
                    <component :is="stat.icon" class="w-6 h-6" :class="stat.color" />
                </div>
                <p class="text-slate-900 mb-1">{{ stat.value }}</p>
                <p class="text-xs text-slate-500">{{ stat.label }}</p>
            </div>
        </div>
    </Card>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import { Card, CardTitle } from './ui/Card.vue'
import { Star, MessageSquare, Users, Heart } from 'lucide-vue-next'

interface StatsData {
    averageRating: number
    totalReviews: number
    totalReaders: number
    totalFavourites: number
}

const props = defineProps<{
    editionId: number | null
}>()

const stats = ref<StatsData>({
    averageRating: 0,
    totalReviews: 0,
    totalReaders: 0,
    totalFavourites: 0,
})

const fetchStats = async (id: number) => {
    try {
        const { data } = await axios.get(`/api/book-editions/${id}`)
        const edition = data.data
        stats.value = {
            averageRating: edition.average_rating ?? 0,
            totalReviews: edition.reviews_count ?? 0,
            totalReaders: edition.readers_count ?? 0,
            totalFavourites: edition.favourites_count ?? 0,
        }
    } catch {
        stats.value = { averageRating: 0, totalReviews: 0, totalReaders: 0, totalFavourites: 0 }
    }
}

onMounted(() => {
    if (props.editionId) fetchStats(props.editionId)
})

watch(() => props.editionId, (newId) => {
    if (newId) fetchStats(newId)
})

const statsArray = computed(() => [
    { icon: Star, label: 'Average Rating', value: stats.value.averageRating.toFixed(1), color: 'text-amber-500' },
    { icon: MessageSquare, label: 'Reviews', value: stats.value.totalReviews.toLocaleString(), color: 'text-blue-500' },
    { icon: Users, label: 'Readers', value: stats.value.totalReaders.toLocaleString(), color: 'text-green-500' },
    { icon: Heart, label: 'Favourites', value: stats.value.totalFavourites.toLocaleString(), color: 'text-rose-500' },
])
</script>
