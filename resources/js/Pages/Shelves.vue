<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

import Button from '@/Components/ui/Button.vue'
import { BookOpen, Heart, ArrowLeft, Settings, BarChart3 } from 'lucide-vue-next'

interface ShelfBook {
    id: number
    status: 'read' | 'reading' | 'tbr' | 'dnf'
    times_read: number
    favourite: boolean
    notes: string | null
    updated_at: string
    book: {
        id: number
        title: string
        author: string
        cover_url: string | null
    }
    edition: {
        id: number
        edition_title: string
        cover_url: string | null
        format: string
    }
}

const books = ref<ShelfBook[]>([])
const loading = ref(false)
const page = ref(1)
const lastPage = ref(1)

const sortBy = ref<'recent' | 'title' | 'author'>('recent')
const showFavouritesOnly = ref(false)

function goBack() {
    router.visit('/home')
}

function openBook(bookId: number, editionId: number) {
    router.visit(`/books/${bookId}/editions/${editionId}`)
}

function goToSettings() {
    router.visit('/settings')
}

function goToStats() {
    router.visit('/stats')
}

async function fetchBooks() {
    if (loading.value || page.value > lastPage.value) return

    loading.value = true

    const token = localStorage.getItem('token')

    const res = await fetch(`/api/my-shelves?page=${page.value}`, {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })

    const json = await res.json()

    lastPage.value = json.meta.last_page

    const mapped = json.data.map((item: any) => ({
        id: item.id,
        status: item.status,
        times_read: item.times_read,
        favourite: item.favourite,
        notes: item.notes,
        updated_at: item.updated_at,
        book: item.edition.book,
        edition: item.edition
    }))

    books.value.push(...mapped)

    page.value++
    loading.value = false
}

function handleScroll() {
    const bottom =
        window.innerHeight + window.scrollY >= document.body.offsetHeight - 200

    if (bottom) {
        fetchBooks()
    }
}

onMounted(() => {
    fetchBooks()
    window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})

const filteredBooks = computed(() => {
    let result = books.value

    if (showFavouritesOnly.value) {
        result = result.filter(b => b.favourite)
    }

    switch (sortBy.value) {
        case 'title':
            return [...result].sort((a, b) => a.book.title.localeCompare(b.book.title))
        case 'author':
            return [...result].sort((a, b) => a.book.author.localeCompare(b.book.author))
        default:
            return [...result].sort(
                (a, b) => new Date(b.updated_at).getTime() - new Date(a.updated_at).getTime()
            )
    }
})

const counts = computed(() => ({
    all: books.value.length,
    read: books.value.filter(b => b.status === 'read').length,
    reading: books.value.filter(b => b.status === 'reading').length,
    tbr: books.value.filter(b => b.status === 'tbr').length,
    dnf: books.value.filter(b => b.status === 'dnf').length
}))
</script>

<template>
    <div class="min-h-screen bg-slate-50">

        <div class="bg-slate-900 text-white">
            <div class="max-w-7xl mx-auto px-4 py-10">

                <div class="flex justify-end gap-4 mb-4">
                    <Button variant="ghost" class="text-white hover:bg-white/10 gap-2" @click="goToStats">
                        <BarChart3 class="w-5 h-5" />
                        Stats
                    </Button>

                    <Button variant="ghost" class="text-white hover:bg-white/10 gap-2" @click="goToSettings">
                        <Settings class="w-5 h-5" />
                        Settings
                    </Button>
                </div>

                <Button variant="ghost" class="text-white mb-4" @click="goBack">
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Back
                </Button>

                <h1 class="text-4xl mb-6 flex items-center gap-2">
                    <BookOpen class="w-8 h-8" />
                    My Library
                </h1>

                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div class="bg-white/10 p-4 text-center rounded">
                        <p class="text-2xl">{{ counts.all }}</p>
                        <p class="text-sm">All</p>
                    </div>
                    <div class="bg-green-500/20 p-4 text-center rounded">
                        <p class="text-2xl">{{ counts.read }}</p>
                        <p class="text-sm">Read</p>
                    </div>
                    <div class="bg-blue-500/20 p-4 text-center rounded">
                        <p class="text-2xl">{{ counts.reading }}</p>
                        <p class="text-sm">Reading</p>
                    </div>
                    <div class="bg-amber-500/20 p-4 text-center rounded">
                        <p class="text-2xl">{{ counts.tbr }}</p>
                        <p class="text-sm">TBR</p>
                    </div>
                    <div class="bg-slate-500/20 p-4 text-center rounded">
                        <p class="text-2xl">{{ counts.dnf }}</p>
                        <p class="text-sm">DNF</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-8">

            <div class="flex gap-4 mb-6">
                <Button
                    :variant="showFavouritesOnly ? 'default' : 'outline'"
                    @click="showFavouritesOnly = !showFavouritesOnly"
                >
                    <Heart class="w-4 h-4 mr-2" />
                    Favourites
                </Button>

                <select v-model="sortBy" class="border rounded px-3 py-2">
                    <option value="recent">Recent</option>
                    <option value="title">Title</option>
                    <option value="author">Author</option>
                </select>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div
                    v-for="b in filteredBooks"
                    :key="b.id"
                    class="bg-white p-4 rounded shadow cursor-pointer"
                    @click="openBook(b.book.id, b.edition.id)"
                >
                    <p class="text-lg">{{ b.book.title }}</p>
                    <p class="text-sm text-slate-500">{{ b.book.author }}</p>
                    <p class="text-xs mt-2">{{ b.edition.format }}</p>
                </div>
            </div>

            <div v-if="loading" class="text-center text-slate-500 mt-6">
                Loading more...
            </div>

        </div>

    </div>
</template>
