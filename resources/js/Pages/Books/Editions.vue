<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import Button from '@/Components/ui/Button.vue'
import { BookOpen } from 'lucide-vue-next'

import type { Edition } from '@/types/edition'

const bookId = Number(window.location.pathname.split('/')[2])

const editions = ref<Edition[]>([])
const loading = ref(true)

const selectedFormat = ref<string | null>(null)
const languageFilter = ref('')

function goBack() {
    window.history.length > 1 ? window.history.back() : router.visit('/home')
}

function openEdition(id: number) {
    localStorage.setItem('lastEditionId', String(id))
    router.visit(`/books/${bookId}/editions/${id}`)
}

onMounted(async () => {
    try {
        const res = await fetch(`/api/books/${bookId}`)
        const json = await res.json()
        editions.value = json.data.editions ?? []
    } catch (e) {
        editions.value = []
    } finally {
        loading.value = false
    }
})

const formats = computed(() => {
    return [...new Set(editions.value.map(e => e.format))]
})

const filteredEditions = computed(() => {
    return editions.value.filter(e => {
        const matchLanguage = e.edition_language
            ?.toLowerCase()
            .includes(languageFilter.value.toLowerCase())

        const matchFormat = selectedFormat.value
            ? e.format === selectedFormat.value
            : true

        return matchLanguage && matchFormat
    })
})

function toggleFormat(format: string) {
    selectedFormat.value =
        selectedFormat.value === format ? null : format
}
</script>

<template>
    <div class="min-h-screen bg-slate-50">

        <div class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">
            <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">

                <div class="flex justify-between items-center mb-6">
                    <Button
                        variant="ghost"
                        class="text-white hover:bg-white/10 px-6 py-2"
                        @click="goBack"
                    >
                        ← Back
                    </Button>

                    <Button
                        class="bg-white text-slate-900 hover:bg-slate-100 px-4 py-2"
                        @click="router.visit(`/book-editions/create/${bookId}`)"
                    >
                        Add Edition
                    </Button>
                </div>

                <h1 class="text-2xl font-semibold">
                    Browse Editions
                </h1>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">

            <div class="bg-white rounded-xl border shadow p-6 mb-8 space-y-4">

                <input
                    v-model="languageFilter"
                    placeholder="Filter by language"
                    class="w-full border px-3 py-2 rounded-lg"
                />

                <div class="flex flex-wrap gap-4">
                    <label
                        v-for="format in formats"
                        :key="format"
                        class="flex items-center gap-2 cursor-pointer"
                    >
                        <input
                            type="checkbox"
                            :checked="selectedFormat === format"
                            @change="toggleFormat(format)"
                        />
                        <span class="text-sm">{{ format }}</span>
                    </label>
                </div>
            </div>

            <div v-if="!loading" class="space-y-4">

                <div
                    v-for="edition in filteredEditions"
                    :key="edition.id"
                    class="bg-white rounded-xl border shadow p-4 flex gap-6 items-start cursor-pointer hover:shadow-lg transition"
                    @click="openEdition(edition.id)"
                >
                    <div class="w-24 h-36 bg-slate-200 rounded-lg overflow-hidden flex items-center justify-center">
                        <img
                            v-if="edition.cover_url"
                            :src="edition.cover_url"
                            class="w-full h-full object-cover"
                        />
                        <BookOpen v-else class="w-6 h-6 text-slate-400" />
                    </div>

                    <div class="flex-1 space-y-2">
                        <h3 class="text-lg font-semibold text-slate-900 hover:underline">
                            {{ edition.edition_title || edition.book.title }}
                        </h3>

                        <p class="text-sm text-slate-500">
                            {{ edition.book.author }}
                        </p>

                        <div class="flex flex-wrap gap-4 text-sm text-slate-600">
                            <span>{{ edition.edition_language }}</span>
                            <span>{{ edition.format }}</span>
                            <span v-if="edition.publisher">{{ edition.publisher }}</span>
                            <span v-if="edition.edition_publication_date">
                                {{ edition.edition_publication_date }}
                            </span>
                        </div>
                    </div>
                </div>

                <div
                    v-if="filteredEditions.length === 0"
                    class="text-center text-slate-500 py-10"
                >
                    No editions found
                </div>
            </div>

            <div v-else class="text-center py-10">
                Loading
            </div>
        </div>
    </div>
</template>
