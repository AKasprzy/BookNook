<script setup lang="ts">
defineProps<{
    genres: { id: number; name: string }[]
    motifs: { id: number; name: string }[]
}>()
</script>

<template>
    <div class="bg-white shadow rounded-xl p-6">

        <div v-if="genres.length > 0" class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-slate-600">🏷️</span>
                <h3 class="text-slate-900 font-semibold">Genres</h3>
            </div>

            <div class="flex flex-wrap gap-2">
        <span
            v-for="genre in genres"
            :key="genre.id"
            class="border border-emerald-200 bg-emerald-50 text-emerald-700 px-2 py-1 rounded"
        >
          {{ genre.name }}
        </span>
            </div>
        </div>

        <div v-if="motifs.length > 0">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-slate-600">✨</span>
                <h3 class="text-slate-900 font-semibold">Themes & Motifs</h3>
            </div>

            <div class="flex flex-wrap gap-2">
        <span
            v-for="motif in motifs"
            :key="motif.id"
            class="border border-purple-200 bg-purple-50 text-purple-700 px-2 py-1 rounded"
        >
          {{ motif.name }}
        </span>
            </div>
        </div>

    </div>
</template>
