<script setup lang="ts">
import { ref, onMounted, computed } from "vue"
import { router } from "@inertiajs/vue3"
import axios from "@/axios"

import Button from "@/Components/ui/Button.vue"
import Badge from "@/Components/ui/Badge.vue"
import Separator from "@/Components/ui/Separator.vue"
import Input from "@/Components/ui/Input.vue"
import Label from "@/Components/ui/Label.vue"
import Textarea from "@/Components/ui/Textarea.vue"
import Card from "@/Components/ui/Card.vue"

import {
    BookMarked,
    Upload,
    Save,
    Loader2,
    X
} from "lucide-vue-next"

const props = defineProps<{
    availableGenres: { id: number; name: string }[]
    availableMotifs: { id: number; name: string }[]
}>()

const pathParts = window.location.pathname.split('/').filter(Boolean)
const editionId = Number(pathParts[pathParts.length - 1])

const loading = ref(true)
const genreInput = ref("")
const motifInput = ref("")

const form = ref({
    edition_title: "",
    edition_publication_date: "",
    format: "",
    edition_language: "",
    description: "",
    isbn: "",
    page_count: undefined as number | undefined,
    length_minutes: undefined as number | undefined,
    cover_url: "",
    publisher: "",
    genre_ids: [] as number[],
    motif_ids: [] as number[],
    processing: false,
    errors: {} as Record<string, string>
})

const selectedGenres = computed(() =>
    (props.availableGenres || []).filter(g => form.value.genre_ids.includes(g.id))
)

const selectedMotifs = computed(() =>
    (props.availableMotifs || []).filter(m => form.value.motif_ids.includes(m.id))
)

const filteredGenres = computed(() =>
    (props.availableGenres || []).filter(g =>
        g.name.toLowerCase().includes(genreInput.value.toLowerCase()) && !form.value.genre_ids.includes(g.id)
    )
)

const filteredMotifs = computed(() =>
    (props.availableMotifs || []).filter(m =>
        m.name.toLowerCase().includes(motifInput.value.toLowerCase()) && !form.value.motif_ids.includes(m.id)
    )
)

function addGenre(id: number) {
    form.value.genre_ids.push(id)
    genreInput.value = ""
}

function removeGenre(id: number) {
    form.value.genre_ids = form.value.genre_ids.filter(gid => gid !== id)
}

function addMotif(id: number) {
    form.value.motif_ids.push(id)
    motifInput.value = ""
}

function removeMotif(id: number) {
    form.value.motif_ids = form.value.motif_ids.filter(mid => mid !== id)
}

onMounted(async () => {
    try {
        const { data } = await axios.get(`/api/book-editions/${editionId}`)
        const e = data.data
        form.value.edition_title = e.edition_title
        form.value.edition_publication_date = e.edition_publication_date
        form.value.format = e.format
        form.value.edition_language = e.edition_language
        form.value.description = e.description
        form.value.isbn = e.isbn
        form.value.page_count = e.page_count
        form.value.length_minutes = e.length_minutes
        form.value.cover_url = e.cover_url
        form.value.publisher = e.publisher
        form.value.genre_ids = e.genres?.map((g: any) => g.id) || []
        form.value.motif_ids = e.motifs?.map((m: any) => m.id) || []
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
})

async function submit() {
    form.value.processing = true
    form.value.errors = {}
    try {
        await axios.put(`/api/book-editions/${editionId}`, {
            edition_title: form.value.edition_title,
            edition_publication_date: form.value.edition_publication_date,
            format: form.value.format,
            edition_language: form.value.edition_language,
            description: form.value.description,
            isbn: form.value.isbn,
            page_count: form.value.page_count,
            length_minutes: form.value.length_minutes,
            cover_url: form.value.cover_url,
            publisher: form.value.publisher,
            genre_ids: form.value.genre_ids,
            motif_ids: form.value.motif_ids
        })

        router.visit(`/books/1/editions/${editionId}`)
    } catch (e: any) {
        form.value.errors = e.response?.data?.errors || {}
    } finally {
        form.value.processing = false
    }
}
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
            <div v-if="loading" class="flex justify-center py-20">
                <Loader2 class="w-8 h-8 animate-spin text-slate-400" />
            </div>

            <form v-else @submit.prevent="submit" class="space-y-8">
                <Card class="p-8 shadow-lg border-none">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <BookMarked class="w-6 h-6 text-purple-600" />
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900">Edit Edition</h2>
                            <p class="text-sm text-slate-500">Update information for this edition</p>
                        </div>
                    </div>

                    <div class="grid gap-6">
                        <div class="space-y-2">
                            <Label>Edition title *</Label>
                            <Input v-model="form.edition_title" required />
                            <p v-if="form.errors.edition_title" class="text-xs text-rose-500">{{ form.errors.edition_title }}</p>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label>Format *</Label>
                                <select v-model="form.format" class="w-full h-10 border border-slate-200 rounded px-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                                    <option value="print">Print</option>
                                    <option value="digital">E-book</option>
                                    <option value="audio">Audiobook</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <Label>Edition language *</Label>
                                <Input v-model="form.edition_language" required />
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label>Publisher</Label>
                                <Input v-model="form.publisher" />
                            </div>
                            <div class="space-y-2">
                                <Label>Publication date</Label>
                                <Input v-model="form.edition_publication_date" type="date" />
                            </div>
                        </div>

                        <div class="grid md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <Label>ISBN</Label>
                                <Input v-model="form.isbn" />
                            </div>
                            <div class="space-y-2">
                                <Label>Page count</Label>
                                <Input v-model.number="form.page_count" type="number" />
                            </div>
                            <div class="space-y-2">
                                <Label>Length (minutes)</Label>
                                <Input v-model.number="form.length_minutes" type="number" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label>Cover URL</Label>
                            <div class="flex gap-2">
                                <Input v-model="form.cover_url" placeholder="https://..." class="flex-1" />
                                <Button type="button" variant="outline">
                                    <Upload class="w-4 h-4" />
                                </Button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label>Description</Label>
                            <Textarea v-model="form.description" rows="5" />
                        </div>

                        <Separator class="my-4" />

                        <div class="space-y-6">
                            <div class="space-y-2">
                                <Label>Genres</Label>
                                <div class="relative">
                                    <Input v-model="genreInput" placeholder="Add genre..." />
                                    <div v-if="genreInput && filteredGenres.length" class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-md shadow-xl max-h-48 overflow-y-auto">
                                        <button v-for="g in filteredGenres" :key="g.id" type="button" @click="addGenre(g.id)" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-sm">
                                            {{ g.name }}
                                        </button>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2 pt-1">
                                    <Badge v-for="g in selectedGenres" :key="g.id" variant="secondary" class="bg-blue-50 text-blue-700 gap-1 border-blue-200">
                                        {{ g.name }}
                                        <X class="w-3 h-3 cursor-pointer" @click="removeGenre(g.id)" />
                                    </Badge>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>Themes & Motifs</Label>
                                <div class="relative">
                                    <Input v-model="motifInput" placeholder="Add motif..." />
                                    <div v-if="motifInput && filteredMotifs.length" class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-md shadow-xl max-h-48 overflow-y-auto">
                                        <button v-for="m in filteredMotifs" :key="m.id" type="button" @click="addMotif(m.id)" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-sm">
                                            {{ m.name }}
                                        </button>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2 pt-1">
                                    <Badge v-for="m in selectedMotifs" :key="m.id" variant="secondary" class="bg-purple-50 text-purple-700 gap-1 border-purple-200">
                                        {{ m.name }}
                                        <X class="w-3 h-3 cursor-pointer" @click="removeMotif(m.id)" />
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </div>
                </Card>

                <div class="flex justify-end gap-4">
                    <Button type="button" variant="ghost" @click="router.visit(`/books/1/editions/${editionId}`)">
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="form.processing" class="bg-slate-900 text-white px-8 gap-2">
                        <Save v-if="!form.processing" class="w-5 h-5" />
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
