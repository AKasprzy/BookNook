<script setup lang="ts">
import { ref, computed } from "vue"
import { router, Link } from "@inertiajs/vue3"
import axios from "@/axios"

import Button from "@/Components/ui/Button.vue"
import Badge from "@/Components/ui/Badge.vue"
import Separator from "@/Components/ui/Separator.vue"
import Input from "@/Components/ui/Input.vue"
import Label from "@/Components/ui/Label.vue"
import Textarea from "@/Components/ui/Textarea.vue"
import Card from "@/Components/ui/Card.vue"

import {
    BookOpen,
    BookMarked,
    ArrowLeft,
    Upload,
    X,
    Plus
} from "lucide-vue-next"

type Genre = { id: number; name: string }
type Motif = { id: number; name: string }

const props = defineProps<{
    availableGenres: Genre[]
    availableMotifs: Motif[]
    backUrl?: string
}>()

const form = ref({
    title: "",
    original_language: "",
    author: "",
    original_publication_date: "",
    series: "",
    genre_ids: [] as number[],
    motif_ids: [] as number[],
    edition: {
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
    },
    processing: false,
    errors: {} as Record<string, string>
})

const genreInput = ref("")
const motifInput = ref("")

const selectedGenres = computed(() =>
    (props.availableGenres || []).filter(g => form.value.genre_ids.includes(g.id))
)

const selectedMotifs = computed(() =>
    (props.availableMotifs || []).filter(m => form.value.motif_ids.includes(m.id))
)

const filteredGenres = computed(() =>
    (props.availableGenres || []).filter(g =>
        g.name.toLowerCase().includes(genreInput.value.toLowerCase()) &&
        !form.value.genre_ids.includes(g.id)
    )
)

const filteredMotifs = computed(() =>
    (props.availableMotifs || []).filter(m =>
        m.name.toLowerCase().includes(motifInput.value.toLowerCase()) &&
        !form.value.motif_ids.includes(m.id)
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

async function submit() {
    form.value.processing = true
    form.value.errors = {}

    try {
        await axios.post("/api/books", {
            title: form.value.title,
            original_language: form.value.original_language,
            author: form.value.author,
            original_publication_date: form.value.original_publication_date,
            series: form.value.series,
            genre_ids: form.value.genre_ids,
            motif_ids: form.value.motif_ids,
            edition: form.value.edition
        })

        router.visit("/home")
    } catch (e: any) {
        form.value.errors = e.response?.data?.errors || {}
    } finally {
        form.value.processing = false
    }
}
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <div class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=1200')] opacity-10 bg-cover bg-center" />

            <div class="relative max-w-5xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <Link :href="backUrl ?? '/'">
                        <Button variant="ghost" class="text-white hover:bg-white/10 gap-2">
                            <ArrowLeft class="w-4 h-4" />
                            Back
                        </Button>
                    </Link>
                    <div>
                        <h1 class="text-3xl font-bold">Add New Book</h1>
                        <p class="text-slate-300">Create a new book entry along with its edition</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
            <form @submit.prevent="submit" class="space-y-8">

                <Card class="p-8 shadow-lg border-none">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <BookOpen class="w-6 h-6 text-blue-600" />
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900">Book Information</h2>
                            <p class="text-sm text-slate-500">Basic data of the original work</p>
                        </div>
                    </div>

                    <div class="grid gap-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="title">Original title *</Label>
                                <Input id="title" v-model="form.title" placeholder="e.g. Pride and Prejudice" required />
                                <p v-if="form.errors.title" class="text-xs text-rose-500">{{ form.errors.title }}</p>
                            </div>
                            <div class="space-y-2">
                                <Label for="author">Author *</Label>
                                <Input id="author" v-model="form.author" placeholder="e.g. Jane Austen" required />
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="lang">Original language *</Label>
                                <Input id="lang" v-model="form.original_language" placeholder="e.g. English" required />
                            </div>
                            <div class="space-y-2">
                                <Label for="pub_date">First publication date</Label>
                                <Input id="pub_date" v-model="form.original_publication_date" type="date" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="series">Series</Label>
                            <Input id="series" v-model="form.series" placeholder="e.g. Chronicles of Narnia" />
                        </div>

                        <Separator class="my-2" />

                        <div class="space-y-4">
                            <div class="space-y-2">
                                <Label>Genres</Label>
                                <div class="relative">
                                    <Input v-model="genreInput" placeholder="Search and add genre..." />
                                    <div v-if="genreInput && filteredGenres.length" class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-md shadow-xl max-h-48 overflow-y-auto">
                                        <button v-for="g in filteredGenres" :key="g.id" type="button" @click="addGenre(g.id)" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-sm">
                                            {{ g.name }}
                                        </button>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2 pt-1">
                                    <Badge v-for="g in selectedGenres" :key="g.id" variant="secondary" class="bg-blue-50 text-blue-700 hover:bg-blue-100 gap-1 border-blue-200">
                                        {{ g.name }}
                                        <X class="w-3 h-3 cursor-pointer" @click="removeGenre(g.id)" />
                                    </Badge>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>Themes & Motifs</Label>
                                <div class="relative">
                                    <Input v-model="motifInput" placeholder="Search and add motif..." />
                                    <div v-if="motifInput && filteredMotifs.length" class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-md shadow-xl max-h-48 overflow-y-auto">
                                        <button v-for="m in filteredMotifs" :key="m.id" type="button" @click="addMotif(m.id)" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-sm">
                                            {{ m.name }}
                                        </button>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2 pt-1">
                                    <Badge v-for="m in selectedMotifs" :key="m.id" variant="secondary" class="bg-purple-50 text-purple-700 hover:bg-purple-100 gap-1 border-purple-200">
                                        {{ m.name }}
                                        <X class="w-3 h-3 cursor-pointer" @click="removeMotif(m.id)" />
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </div>
                </Card>

                <Card class="p-8 shadow-lg border-none">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <BookMarked class="w-6 h-6 text-purple-600" />
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900">Edition Information</h2>
                            <p class="text-sm text-slate-500">Details of a specific edition (e.g. your copy)</p>
                        </div>
                    </div>

                    <div class="grid gap-6">
                        <div class="space-y-2">
                            <Label for="e_title">Edition title *</Label>
                            <Input id="e_title" v-model="form.edition.edition_title" placeholder="e.g. Anniversary Edition" required />
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="format">Format *</Label>
                                <select id="format" v-model="form.edition.format" class="w-full flex h-10 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm ring-offset-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950" required>
                                    <option value="" disabled>Select format</option>
                                    <option value="print">Print</option>
                                    <option value="digital">E-book</option>
                                    <option value="audio">Audiobook</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <Label for="e_lang">Edition language *</Label>
                                <Input id="e_lang" v-model="form.edition.edition_language" placeholder="e.g. English" required />
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="publisher">Publisher</Label>
                                <Input id="publisher" v-model="form.edition.publisher" />
                            </div>
                            <div class="space-y-2">
                                <Label for="e_pub_date">Edition publication date</Label>
                                <Input id="e_pub_date" v-model="form.edition.edition_publication_date" type="date" />
                            </div>
                        </div>

                        <div class="grid md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <Label for="isbn">ISBN</Label>
                                <Input id="isbn" v-model="form.edition.isbn" placeholder="e.g. 978..." />
                            </div>
                            <div class="space-y-2">
                                <Label for="pages">Page count</Label>
                                <Input id="pages" v-model.number="form.edition.page_count" type="number" />
                            </div>
                            <div class="space-y-2">
                                <Label for="length">Length (minutes)</Label>
                                <Input id="length" v-model.number="form.edition.length_minutes" type="number" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="cover">Cover URL</Label>
                            <div class="flex gap-2">
                                <Input id="cover" v-model="form.edition.cover_url" placeholder="https://..." class="flex-1" />
                                <Button type="button" variant="outline" class="gap-2">
                                    <Upload class="w-4 h-4" />
                                    Upload
                                </Button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Edition description</Label>
                            <Textarea id="description" v-model="form.edition.description" rows="5" placeholder="Add a short description of this edition..." />
                        </div>
                    </div>
                </Card>

                <div class="flex justify-end gap-4">
                    <Button type="button" variant="ghost" @click="router.visit(backUrl ?? '/')">
                        Cancel
                    </Button>
                    <Button type="submit" size="lg" :disabled="form.processing" class="bg-slate-900 text-white gap-2 px-8">
                        <Plus v-if="!form.processing" class="w-5 h-5" />
                        {{ form.processing ? 'Creating...' : 'Create Book and Edition' }}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
