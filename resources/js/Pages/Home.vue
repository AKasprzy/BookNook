<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { fetchBookEditionCount, fetchUserCount, fetchReviewCount } from '@/Services/statsService'

import DatabaseOverview from '@/Components/DatabaseOverview.vue'
import RecentReviews from '@/Components/RecentReviews.vue'
import Button from '@/Components/ui/Button.vue'
import Input from '@/Components/ui/Input.vue'
import { Link, router } from '@inertiajs/vue3'
import { BookOpen, Users, Star, Search, Library, LogOut } from 'lucide-vue-next'

const token = localStorage.getItem('token')
const user = ref(!!token)

const latestBooks = ref<any[]>([])

function logout() {
    localStorage.removeItem('token')
    window.location.href = '/'
}

function goToSearch() {
    router.visit('/search')
}

function openBook(bookId: number, editionId: number) {
    router.visit(`/books/${bookId}/editions/${editionId}`)
}

const stats = ref([
    { icon: BookOpen, label: 'Total Books', value: '...', color: 'bg-blue-500' },
    { icon: Users, label: 'Users Registered', value: '...', color: 'bg-green-500' },
    { icon: Star, label: 'Reviews Published', value: '...', color: 'bg-amber-500' },
])

onMounted(async () => {
    try {
        const bookData = await fetchBookEditionCount()
        const userData = await fetchUserCount()
        const reviewData = await fetchReviewCount()

        stats.value[0].value = String(bookData.total_editions)
        stats.value[1].value = String(userData.total_users)
        stats.value[2].value = String(reviewData.total_reviews)

        const res = await fetch('/api/books/latest')
        const json = await res.json()
        latestBooks.value = json.data
    } catch (e) {
        stats.value[0].value = '0'
        stats.value[1].value = '0'
        stats.value[2].value = '0'
        latestBooks.value = []
    }
})
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <div class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=1200')] opacity-10 bg-cover bg-center" />

            <div class="relative border-b border-white/10">
                <div class="max-w-7xl mx-auto px-4 py-4 flex justify-end items-center">
                    <template v-if="user">
                        <div class="flex items-center gap-6">
                            <Link href="/shelves">
                                <Button variant="ghost" class="text-white hover:bg-white/10 gap-2">
                                    <Library class="w-5 h-5" />
                                    My Library
                                </Button>
                            </Link>

                            <button @click="logout">
                                <Button variant="ghost" class="text-white hover:bg-white/10 gap-2">
                                    <LogOut class="w-5 h-5" />
                                    Logout
                                </Button>
                            </button>
                        </div>
                    </template>

                    <template v-else>
                        <Link href="/login">
                            <Button variant="ghost" class="text-white hover:bg-white/10">
                                Login
                            </Button>
                        </Link>
                    </template>
                </div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 py-24 text-center space-y-6">
                <h1 class="text-5xl md:text-6xl">
                    Track Your Reading Journey
                </h1>

                <p class="text-xl text-slate-300">
                    Discover, review, and analyze your reading habits.
                </p>

                <div class="max-w-2xl mx-auto pt-4">
                    <div class="relative">
                        <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                        <Input
                            placeholder="Search books, authors..."
                            class="pl-12 h-14 bg-white/95"
                            @click="goToSearch"
                        />
                    </div>
                </div>

                <div class="flex flex-wrap justify-center gap-4 pt-6">
                    <Link href="/books/create">
                        <Button size="lg" class="bg-white text-slate-900 px-8 py-4 text-lg gap-2">
                            <BookOpen class="w-6 h-6" />
                            Add Book
                        </Button>
                    </Link>

                    <Link href="/search">
                        <Button size="lg" variant="outline" class="border-white/30 text-white px-8 py-4 text-lg">
                            Browse Books
                        </Button>
                    </Link>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 mt-8 grid md:grid-cols-3 gap-4">
            <div v-for="stat in stats" :key="stat.label" class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center gap-4">
                    <div :class="stat.color" class="p-3 rounded-lg">
                        <component :is="stat.icon" class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">{{ stat.label }}</p>
                        <p class="text-2xl text-slate-900">{{ stat.value }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-12 space-y-12">
            <DatabaseOverview />

            <section>
                <div class="flex justify-between mb-6">
                    <div>
                        <h2 class="text-slate-900">Newly Added Books</h2>
                        <p class="text-slate-600">Fresh additions to your library</p>
                    </div>
                </div>

                <div class="flex gap-4 overflow-x-auto pb-2">
                    <div
                        v-for="book in latestBooks"
                        :key="book.id"
                        class="min-w-[220px] bg-white rounded-lg shadow-md overflow-hidden cursor-pointer flex flex-col"
                        @click="openBook(book.id, book.editions[0]?.id)"
                    >
                        <div class="w-full aspect-[3/4] bg-slate-200 overflow-hidden flex items-center justify-center">
                            <img
                                v-if="book.editions?.[0]?.cover_url"
                                :src="book.editions[0].cover_url"
                                class="w-full h-full object-cover"
                            />
                            <BookOpen v-else class="w-10 h-10 text-slate-400" />
                        </div>

                        <div class="p-4 flex-1 flex flex-col">
                            <h3 class="text-slate-900 line-clamp-1">
                                {{ book.title }}
                            </h3>

                            <p class="text-sm text-slate-600">
                                {{ book.author }}
                            </p>

                            <p class="text-xs text-slate-500 mt-2 line-clamp-3">
                                {{ book.editions?.[0]?.description ?? 'No description available' }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <div class="flex justify-between mb-6">
                    <div>
                        <h2 class="text-slate-900">Latest Community Reviews</h2>
                        <p class="text-slate-600">What readers are saying</p>
                    </div>
                </div>
                <RecentReviews />
            </section>
        </div>
    </div>
</template>
