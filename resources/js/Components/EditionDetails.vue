<script setup lang="ts">
const props = defineProps<{
    edition: {
        publisher?: string
        edition_publication_date?: string
        page_count?: number
        length_minutes?: number
        edition_language?: string
    }
}>()

function formatDate(date?: string) {
    if (!date) return null
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

function formatLength(minutes?: number) {
    if (!minutes) return null
    const h = Math.floor(minutes / 60)
    const m = minutes % 60
    return `${h}h ${m}m`
}
</script>

<template>
    <div class="bg-white shadow rounded-xl p-6">
        <h2 class="text-slate-900 mb-6 font-semibold">Edition Details</h2>

        <div class="space-y-4">

            <div v-if="edition.publisher" class="flex items-start gap-3">
                <span class="text-slate-500">🏢</span>
                <div>
                    <p class="text-sm text-slate-500">Publisher</p>
                    <p class="text-slate-900 break-words">{{ edition.publisher }}</p>
                </div>
            </div>

            <hr v-if="edition.publisher" />

            <div v-if="formatDate(edition.edition_publication_date)" class="flex items-start gap-3">
                <span class="text-slate-500">📅</span>
                <div>
                    <p class="text-sm text-slate-500">Publication Date</p>
                    <p class="text-slate-900">
                        {{ formatDate(edition.edition_publication_date) }}
                    </p>
                </div>
            </div>

            <hr v-if="formatDate(edition.edition_publication_date)" />

            <div v-if="edition.page_count" class="flex items-start gap-3">
                <span class="text-slate-500">📖</span>
                <div>
                    <p class="text-sm text-slate-500">Pages</p>
                    <p class="text-slate-900">
                        {{ edition.page_count }} pages
                    </p>
                </div>
            </div>

            <hr v-if="edition.page_count" />

            <div v-if="formatLength(edition.length_minutes)" class="flex items-start gap-3">
                <span class="text-slate-500">⏱️</span>
                <div>
                    <p class="text-sm text-slate-500">Length</p>
                    <p class="text-slate-900">
                        {{ formatLength(edition.length_minutes) }}
                    </p>
                </div>
            </div>

            <hr v-if="formatLength(edition.length_minutes)" />

            <div v-if="edition.edition_language" class="flex items-start gap-3">
                <span class="text-slate-500">🌍</span>
                <div>
                    <p class="text-sm text-slate-500">Language</p>
                    <p class="text-slate-900">
                        {{ edition.edition_language }}
                    </p>
                </div>
            </div>

        </div>
    </div>
</template>
