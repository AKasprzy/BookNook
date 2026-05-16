<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { router } from '@inertiajs/vue3'

import Button from '@/Components/ui/Button.vue'
import { ArrowLeft, BarChart3 } from 'lucide-vue-next'

import { Bar, Pie, Line } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    ArcElement,
    LineElement,
    CategoryScale,
    LinearScale,
    PointElement
} from 'chart.js'

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    ArcElement,
    LineElement,
    CategoryScale,
    LinearScale,
    PointElement
)

const stats = ref<any>(null)
const loading = ref(true)

function goBack() {
    router.visit('/shelves')
}

const totalBooks = computed(() => {
    if (!stats.value) return 0
    return Object.values(stats.value.status_distribution).reduce((a: any, b: any) => a + b, 0)
})

const totalAuthors = computed(() => stats.value?.top_authors?.length ?? 0)

const statusColors = [
    '#22c55e',
    '#3b82f6',
    '#f59e0b',
    '#64748b'
]

const defaultColors = [
    '#6366f1',
    '#22c55e',
    '#3b82f6',
    '#f59e0b',
    '#ef4444',
    '#8b5cf6',
    '#14b8a6'
]

const statusData = ref<any>(null)
const formatData = ref<any>(null)
const authorsData = ref<any>(null)
const readingData = ref<any>(null)
const favouritesData = ref<any>(null)
const timesReadData = ref<any>(null)
const booksYearData = ref<any>(null)
const languagesData = ref<any>(null)
const pagesData = ref<any>(null)

onMounted(async () => {
    try {
        const res = await fetch('/api/user/stats', {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
        })

        const json = await res.json()
        stats.value = json

        statusData.value = {
            labels: Object.keys(json.status_distribution),
            datasets: [{
                data: Object.values(json.status_distribution),
                backgroundColor: statusColors
            }]
        }

        formatData.value = {
            labels: Object.keys(json.formats),
            datasets: [{
                data: Object.values(json.formats),
                backgroundColor: defaultColors
            }]
        }

        authorsData.value = {
            labels: json.top_authors.map((a:any)=>a.author),
            datasets: [{
                label: 'Books',
                data: json.top_authors.map((a:any)=>a.count),
                backgroundColor: '#3b82f6'
            }]
        }

        readingData.value = {
            labels: json.reading_over_time.map((r:any)=>r.date),
            datasets: [{
                label: 'Books',
                data: json.reading_over_time.map((r:any)=>r.count),
                borderColor: '#6366f1'
            }]
        }

        favouritesData.value = {
            labels: ['Favourite', 'Not Favourite'],
            datasets: [{
                data: [
                    json.favourites_ratio.favourite,
                    json.favourites_ratio.not_favourite
                ],
                backgroundColor: ['#ef4444', '#94a3b8']
            }]
        }

        timesReadData.value = {
            labels: json.times_read_distribution.map((r:any)=>`${r.times_read}x`),
            datasets: [{
                label: 'Books',
                data: json.times_read_distribution.map((r:any)=>r.count),
                backgroundColor: '#22c55e'
            }]
        }

        booksYearData.value = {
            labels: json.books_per_year.map((y:any)=>y.year),
            datasets: [{
                label: 'Books',
                data: json.books_per_year.map((y:any)=>y.count),
                backgroundColor: '#6366f1'
            }]
        }

        languagesData.value = {
            labels: Object.keys(json.languages),
            datasets: [{
                data: Object.values(json.languages),
                backgroundColor: defaultColors
            }]
        }

        pagesData.value = {
            labels: Object.keys(json.pages_distribution),
            datasets: [{
                label: 'Books',
                data: Object.values(json.pages_distribution),
                backgroundColor: '#f59e0b'
            }]
        }

    } catch (e) {
        stats.value = null
    } finally {
        loading.value = false
    }
})
</script>

<template>
    <div class="min-h-screen bg-slate-50">

        <div class="bg-slate-900 text-white">
            <div class="max-w-5xl mx-auto px-4 py-10">

                <Button variant="ghost" class="text-white mb-4" @click="goBack">
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Back
                </Button>

                <h1 class="text-4xl flex items-center gap-2">
                    <BarChart3 class="w-8 h-8" />
                    Statistics
                </h1>

                <p class="text-slate-300 mt-2">
                    Insights into your reading habits
                </p>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-8">
                    <div class="bg-white/10 p-4 rounded text-center">
                        <p class="text-3xl">{{ totalBooks }}</p>
                        <p class="text-sm text-slate-300">Total Books</p>
                    </div>

                    <div class="bg-white/10 p-4 rounded text-center">
                        <p class="text-3xl">{{ totalAuthors }}</p>
                        <p class="text-sm text-slate-300">Top Authors Tracked</p>
                    </div>

                    <div class="bg-white/10 p-4 rounded text-center">
                        <p class="text-3xl">
                            {{ stats?.favourites_ratio?.favourite ?? 0 }}
                        </p>
                        <p class="text-sm text-slate-300">Favourites</p>
                    </div>
                </div>

            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 py-10 space-y-10">

            <div v-if="loading" class="text-center text-slate-500">
                Loading statistics...
            </div>

            <div v-else-if="!stats" class="text-center text-red-500">
                Failed to load statistics
            </div>

            <template v-else>

                <div class="bg-white p-6 rounded shadow">
                    <h2 class="mb-4">Formats</h2>
                    <Pie :data="formatData" />
                </div>

                <div class="bg-white p-6 rounded shadow">
                    <h2 class="mb-4">Top Authors</h2>
                    <Bar :data="authorsData" />
                </div>

                <div class="bg-white p-6 rounded shadow">
                    <h2 class="mb-4">Reading Over Time</h2>
                    <Line :data="readingData" />
                </div>

                <div class="bg-white p-6 rounded shadow">
                    <h2 class="mb-4">Favourites Ratio</h2>
                    <Pie :data="favouritesData" />
                </div>

                <div class="bg-white p-6 rounded shadow">
                    <h2 class="mb-4">Times Read</h2>
                    <Bar :data="timesReadData" />
                </div>

                <div class="bg-white p-6 rounded shadow">
                    <h2 class="mb-4">Books Per Year</h2>
                    <Bar :data="booksYearData" />
                </div>

                <div class="bg-white p-6 rounded shadow">
                    <h2 class="mb-4">Languages</h2>
                    <Pie :data="languagesData" />
                </div>

                <div class="bg-white p-6 rounded shadow">
                    <h2 class="mb-4">Book Length</h2>
                    <Bar :data="pagesData" />
                </div>

                <div class="bg-white p-6 rounded shadow">
                    <h2 class="mb-4">Status Distribution</h2>
                    <Pie :data="statusData" />
                </div>

            </template>

        </div>
    </div>
</template>
