<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Bar } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const loaded = ref(false)

const booksPerYearData = ref({
    labels: [] as string[],
    datasets: [
        {
            label: 'Books Published',
            data: [] as number[]
        }
    ]
})

const booksByFormatData = ref({
    labels: [] as string[],
    datasets: [
        {
            label: 'Books by Format',
            data: [] as number[]
        }
    ]
})

onMounted(async () => {
    const yearResponse = await fetch('/api/books/per-year')
    const yearJson = await yearResponse.json()
    const yearData = yearJson.data ?? []

    booksPerYearData.value.labels = yearData.map((item:any)=>String(item.year))
    booksPerYearData.value.datasets[0].data = yearData.map((item:any)=>item.total)

    const formatResponse = await fetch('/api/book-editions/by-format')
    const formatJson = await formatResponse.json()
    const formatData = formatJson.data ?? []

    booksByFormatData.value.labels = formatData.map((item:any)=>item.format)
    booksByFormatData.value.datasets[0].data = formatData.map((item:any)=>item.total)

    loaded.value = true
})
</script>

<template>
    <div class="space-y-12">

        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl mb-4">Books Per Year</h2>

            <Bar
                v-if="loaded"
                :data="booksPerYearData"
            />

            <p v-else class="text-slate-500">Loading chart...</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl mb-4">Books by Format</h2>

            <Bar
                v-if="loaded"
                :data="booksByFormatData"
            />

            <p v-else class="text-slate-500">Loading chart...</p>
        </div>

    </div>
</template>
