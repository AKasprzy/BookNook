<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import Button from '@/Components/ui/Button.vue'
import Input from '@/Components/ui/Input.vue'
import { BookOpen, Search } from 'lucide-vue-next'

type Edition = {
    id: number
    format: string
    cover_url?: string | null
    description?: string | null
}

type Book = {
    id: number
    title: string
    author: string
    editions?: Edition[]
}

const query = ref('')

const localResults = ref<Book[]>([])
const externalResults = ref<Book[]>([])
const latestBooks = ref<Book[]>([])

const loading = ref(false)
const typingTimeout = ref<ReturnType<typeof setTimeout> | null>(null)

const selectedFormat = ref<string | null>(null)

async function fetchLatest() {
    try {
        const res = await fetch('/api/books/latest')
        const json = await res.json()
        latestBooks.value = json.data ?? []
    } catch {
        latestBooks.value = []
    }
}

fetchLatest()

async function searchLocal() {
    if (query.value.trim().length < 2) {
        localResults.value = []
        return
    }

    try {
        const res = await fetch(`/api/books?q=${encodeURIComponent(query.value)}`)
        const json = await res.json()
        localResults.value = json.data ?? []
    } catch {
        localResults.value = []
    }
}

async function searchExternal() {
    if (query.value.trim().length < 2) {
        externalResults.value = []
        return
    }

    loading.value = true

    try {
        const res = await fetch(`/api/search?q=${encodeURIComponent(query.value)}`, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
        })

        const data: Book[] = await res.json()

        const localMap = new Map(localResults.value.map(b => [b.title + b.author, b]))

        const exactMatches: Book[] = []
        const others: Book[] = []

        for (const book of data) {
            const key = book.title + book.author
            if (localMap.has(key)) {
                exactMatches.push(localMap.get(key)!)
            } else {
                others.push(book)
            }
        }

        externalResults.value = [...exactMatches, ...others]
    } catch {
        externalResults.value = []
    } finally {
        loading.value = false
    }
}

watch(query, () => {
    if (typingTimeout.value) clearTimeout(typingTimeout.value)
    typingTimeout.value = setTimeout(() => {
        searchLocal()
    }, 300)
})

function triggerSearch() {
    searchExternal()
}

function openBook(bookId: number, editionId: number) {
    router.visit(`/books/${bookId}/editions/${editionId}`)
}

const displayBooks = computed(() => {
    if (externalResults.value.length > 0) return externalResults.value
    if (query.value.trim().length >= 2) return localResults.value
    return latestBooks.value
})

const formats = computed<string[]>(() => {
    return [
        ...new Set(
            displayBooks.value.flatMap(b =>
                b.editions?.map((e: Edition) => e.format) ?? []
            )
        )
    ]
})

const filteredBooks = computed(() => {
    return displayBooks.value.filter(b => {
        const matchFormat = selectedFormat.value
            ? b.editions?.some((e: Edition) => e.format === selectedFormat.value)
            : true

        return matchFormat
    })
})

function toggleFormat(format: string) {
    selectedFormat.value = selectedFormat.value === format ? null : format
}
</script>

<template>
    <div class="min-h-screen bg-slate-50">

        <div class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">
            <div class="max-w-7xl mx-auto px-4 py-8">

                <div class="flex justify-between items-center mb-6">
                    <Button variant="ghost" class="text-white hover:bg-white/10 px-6 py-2" @click="router.visit('/')">
                        ← Back
                    </Button>

                    <Button class="bg-white text-slate-900 hover:bg-slate-100 px-4 py-2" @click="router.visit('/books/create')">
                        Add Book
                    </Button>
                </div>

                <h1 class="text-2xl font-semibold mb-4">Search Books</h1>

                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                        <Input
                            v-model="query"
                            @keyup.enter="triggerSearch"
                            placeholder="Search books or authors..."
                            class="pl-12 h-12 bg-white text-slate-900"
                        />
                    </div>

                    <Button @click="triggerSearch" class="h-12 px-6">
                        Search
                    </Button>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-8">

            <div class="bg-white rounded-xl border shadow p-6 mb-8">
                <div class="flex flex-wrap gap-4">
                    <label v-for="format in formats" :key="format" class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" :checked="selectedFormat === format" @change="toggleFormat(format)" />
                        <span class="text-sm">{{ format }}</span>
                    </label>
                </div>
            </div>

            <div v-if="!loading" class="space-y-4">

                <div
                    v-for="book in filteredBooks"
                    :key="book.id"
                    class="bg-white rounded-xl border shadow p-4 flex gap-6 items-start cursor-pointer hover:shadow-lg transition"
                    @click="book.editions?.length && openBook(book.id, book.editions[0].id)"
                >
                    <div class="w-24 h-36 bg-slate-200 rounded-lg overflow-hidden flex items-center justify-center">
                        <img
                            v-if="book.editions?.[0]?.cover_url"
                            :src="book.editions[0].cover_url"
                            :alt="book.title"
                            class="w-full h-full object-cover"
                        />
                        <BookOpen v-else class="w-6 h-6 text-slate-400" />
                    </div>

                    <div class="flex-1 space-y-2">
                        <h3 class="text-lg font-semibold text-slate-900 hover:underline">{{ book.title }}</h3>
                        <p class="text-sm text-slate-500">{{ book.author }}</p>

                        <div class="flex flex-wrap gap-4 text-sm text-slate-600">
                            <span v-for="e in book.editions" :key="e.id">{{ e.format }}</span>
                        </div>

                        <p class="text-sm text-slate-600 line-clamp-3">
                            {{ book.editions?.[0]?.description ?? 'No description available' }}
                        </p>
                    </div>
                </div>

                <div v-if="filteredBooks.length === 0 && query" class="text-center text-slate-500 py-10 space-y-4">
                    <p>No books found</p>
                    <Button @click="router.visit('/books/create')">Don’t see your book? Add it</Button>
                </div>
            </div>

            <div v-else class="text-center py-10">Loading</div>
        </div>
    </div>
</template>
