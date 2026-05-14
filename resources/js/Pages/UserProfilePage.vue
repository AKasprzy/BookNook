<script setup lang="ts">
import { computed, ref, onMounted } from "vue"
import { Link, router } from "@inertiajs/vue3"
import axios from "@/axios"

import Button from "@/Components/ui/Button.vue"
import Badge from "@/Components/ui/Badge.vue"
import { Card } from "@/Components/ui/Card.vue"
import Avatar from "@/Components/ui/Avatar.vue"
import AvatarFallback from "@/Components/ui/AvatarFallback.vue"
import Separator from "@/Components/ui/Separator.vue"

import {
    Calendar,
    MapPin,
    Heart,
    BookOpen,
    Star,
    TrendingUp,
    Eye,
    BarChart3,
    Trash2,
    Settings,
    ChevronDown
} from "lucide-vue-next"

type User = {
    id: number
    name: string
    location?: string | null
    joined_at?: string | null
    bio?: string | null
    favourite_genres?: string[] | null
}

type UserShelf = {
    id: number
    status: string
    times_read: number
    favourite: boolean
    read_at?: string | null
    edition: {
        book_id: number
        edition_title: string
        format: string
        description?: string | null
        cover_url?: string | null
        book: {
            id: number
            title: string
            author: string
        }
    }
}

type Review = {
    id: number
    book_id: number
    rating: number | null
    review_text: string | null
    spoiler: boolean
    reread: boolean
    reviewed_at: string
}

const props = defineProps<{
    user: User
    recentlyReadBooks: UserShelf[]
    userReviews: Review[]
    backUrl?: string
    statsUrl?: string
}>()

const token = localStorage.getItem("token")
const userRole = ref<string | null>(null)
const showModMenu = ref(false)

const isModerator = computed(() =>
    ["moderator", "admin", "superAdmin"].includes(userRole.value || "")
)

const isSuperAdmin = computed(() =>
    userRole.value === "superAdmin"
)

const totalBooksRead = computed(() => {
    return props.recentlyReadBooks.filter((s) => s.status === "read").length
})

const totalReviews = computed(() => {
    return props.userReviews.length
})

const averageRating = computed(() => {
    const rated = props.userReviews.filter((r) => r.rating !== null)
    if (rated.length === 0) return 0
    const sum = rated.reduce((acc, r) => acc + (r.rating ?? 0), 0)
    return sum / rated.length
})

const favouriteBooks = computed(() => {
    return props.recentlyReadBooks.filter((s) => s.favourite).length
})

function formatDate(dateString: string) {
    const date = new Date(dateString)
    return new Intl.DateTimeFormat("en-US", {
        day: "numeric",
        month: "long",
        year: "numeric"
    }).format(date)
}

function getInitials(name: string) {
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
}

function stars(rating: number) {
    return Array.from({ length: 5 }, (_, i) => i + 1).map((i) => i <= rating)
}

async function deleteUser() {
    if (!confirm("Permanently delete this user?")) return
    try {
        await axios.delete(`/api/users/${props.user.id}`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        router.visit("/")
    } catch (e) {
        console.error(e)
    }
}

onMounted(async () => {
    if (!token) return
    try {
        const res = await fetch("/api/user", {
            headers: { Authorization: `Bearer ${token}` }
        })
        const data = await res.json()
        userRole.value = data.role
    } catch (e) {
        console.error(e)
    }
})
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <div class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=1200')] opacity-10 bg-cover bg-center" />

            <div class="relative max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <Link :href="backUrl ?? '/'">
                        <Button variant="ghost" class="text-white hover:bg-white/10">
                            ← Back
                        </Button>
                    </Link>

                    <div class="flex items-center gap-2">
                        <Link v-if="statsUrl" :href="statsUrl">
                            <Button class="bg-white text-slate-900 hover:bg-slate-100 gap-2">
                                <BarChart3 class="w-4 h-4" />
                                View Stats
                            </Button>
                        </Link>

                        <div v-if="isModerator" class="relative">
                            <Button class="bg-white/10 hover:bg-white/20 text-white gap-2 border border-white/20" @click="showModMenu = !showModMenu">
                                <Settings class="w-4 h-4" />
                                Manage
                                <ChevronDown class="w-4 h-4" :class="{ 'rotate-180': showModMenu }" />
                            </Button>

                            <div v-if="showModMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border py-1 z-50">
                                <button v-if="isSuperAdmin" @click="deleteUser" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 font-bold">
                                    <Trash2 class="w-4 h-4" />
                                    Force Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-6 items-start">
                    <Avatar class="w-24 h-24 border-4 border-white/20">
                        <AvatarFallback class="text-3xl bg-gradient-to-br from-blue-500 to-purple-600">
                            {{ getInitials(user.name) }}
                        </AvatarFallback>
                    </Avatar>

                    <div class="flex-1 space-y-3">
                        <div>
                            <h1 class="text-3xl md:text-4xl mb-2">
                                {{ user.name }}
                            </h1>

                            <div class="flex flex-wrap gap-4 text-slate-300 text-sm">
                                <div v-if="user.location" class="flex items-center gap-2">
                                    <MapPin class="w-4 h-4" />
                                    <span>{{ user.location }}</span>
                                </div>

                                <div v-if="user.joined_at" class="flex items-center gap-2">
                                    <Calendar class="w-4 h-4" />
                                    <span>Joined {{ formatDate(user.joined_at) }}</span>
                                </div>
                            </div>
                        </div>

                        <p v-if="user.bio" class="text-slate-300 text-sm max-w-2xl line-clamp-2">
                            {{ user.bio }}
                        </p>

                        <div v-if="user.favourite_genres && user.favourite_genres.length > 0" class="flex flex-wrap gap-2">
                            <Badge v-for="(genre, index) in user.favourite_genres" :key="index" variant="secondary" class="bg-white/10 text-white border-white/20 text-xs">
                                {{ genre }}
                            </Badge>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 pt-8 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <Card class="p-6 bg-white shadow-lg">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <BookOpen class="w-6 h-6 text-blue-600" />
                        </div>
                        <div>
                            <p class="text-sm text-slate-600">Books read</p>
                            <p class="text-2xl text-slate-900">{{ totalBooksRead }}</p>
                        </div>
                    </div>
                </Card>

                <Card class="p-6 bg-white shadow-lg">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-amber-100 rounded-lg">
                            <Star class="w-6 h-6 text-amber-600" />
                        </div>
                        <div>
                            <p class="text-sm text-slate-600">Reviews</p>
                            <p class="text-2xl text-slate-900">{{ totalReviews }}</p>
                        </div>
                    </div>
                </Card>

                <Card class="p-6 bg-white shadow-lg">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <TrendingUp class="w-6 h-6 text-purple-600" />
                        </div>
                        <div>
                            <p class="text-sm text-slate-600">Avg. rating</p>
                            <p class="text-2xl text-slate-900">{{ averageRating.toFixed(1) }}</p>
                        </div>
                    </div>
                </Card>

                <Card class="p-6 bg-white shadow-lg">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-rose-100 rounded-lg">
                            <Heart class="w-6 h-6 text-rose-600" />
                        </div>
                        <div>
                            <p class="text-sm text-slate-600">Favourites</p>
                            <p class="text-2xl text-slate-900">{{ favouriteBooks }}</p>
                        </div>
                    </div>
                </Card>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <h2 class="text-slate-900 mb-4">
                            Recently read books
                        </h2>

                        <div class="space-y-4">
                            <Card v-if="recentlyReadBooks.length === 0" class="p-8 text-center">
                                <BookOpen class="w-12 h-12 text-slate-400 mx-auto mb-3" />
                                <p class="text-slate-600">
                                    This user hasn’t read any books yet
                                </p>
                            </Card>

                            <Link
                                v-else
                                v-for="shelf in recentlyReadBooks"
                                :key="shelf.id"
                                :href="`/books/${shelf.edition.book_id}`"
                                class="block"
                            >
                                <Card class="p-6 hover:shadow-lg transition-all cursor-pointer">
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-24 h-36 bg-slate-200 rounded-lg overflow-hidden shadow-md">
                                                <img
                                                    v-if="shelf.edition.cover_url"
                                                    :src="shelf.edition.cover_url"
                                                    :alt="shelf.edition.edition_title"
                                                    class="w-full h-full object-cover"
                                                />
                                                <div v-else class="w-full h-full flex items-center justify-center">
                                                    <BookOpen class="w-8 h-8 text-slate-400" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2 mb-2">
                                                <div class="flex-1 min-w-0">
                                                    <h3 class="text-slate-900 truncate">
                                                        {{ shelf.edition.book.title }}
                                                    </h3>
                                                    <p class="text-slate-600">
                                                        {{ shelf.edition.book.author }}
                                                    </p>
                                                </div>

                                                <Heart
                                                    v-if="shelf.favourite"
                                                    class="w-5 h-5 text-rose-500 fill-rose-500 flex-shrink-0"
                                                />
                                            </div>

                                            <div class="flex flex-wrap gap-2 mb-3">
                                                <Badge variant="outline" class="text-xs">
                                                    {{ shelf.edition.format }}
                                                </Badge>

                                                <Badge v-if="shelf.read_at" variant="outline" class="text-xs">
                                                    <Eye class="w-3 h-3 mr-1" />
                                                    {{ formatDate(shelf.read_at) }}
                                                </Badge>

                                                <Badge v-if="shelf.times_read > 1" variant="secondary" class="text-xs">
                                                    Read {{ shelf.times_read }}×
                                                </Badge>
                                            </div>

                                            <p
                                                v-if="shelf.edition.description"
                                                class="text-sm text-slate-600 line-clamp-2"
                                            >
                                                {{ shelf.edition.description }}
                                            </p>
                                        </div>
                                    </div>
                                </Card>
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <h2 class="text-slate-900 mb-4">
                        User reviews
                    </h2>

                    <div class="space-y-4">
                        <Card v-if="userReviews.length === 0" class="p-6 text-center">
                            <Star class="w-8 h-8 text-slate-400 mx-auto mb-2" />
                            <p class="text-sm text-slate-600">
                                This user hasn’t written any reviews yet
                            </p>
                        </Card>

                        <Link
                            v-else
                            v-for="review in userReviews"
                            :key="review.id"
                            :href="`/books/${review.book_id}`"
                            class="block"
                        >
                            <Card class="p-4 hover:shadow-md transition-shadow cursor-pointer">
                                <div class="space-y-3">
                                    <div v-if="review.rating !== null" class="flex items-center gap-1">
                                        <Star
                                            v-for="(filled, idx) in stars(review.rating)"
                                            :key="idx"
                                            class="w-4 h-4"
                                            :class="filled
                                                ? 'fill-amber-400 text-amber-400'
                                                : 'text-slate-300'"
                                        />
                                    </div>

                                    <div v-if="review.review_text">
                                        <Badge v-if="review.spoiler" variant="destructive" class="mb-2 text-xs">
                                            Spoiler
                                        </Badge>

                                        <p class="text-sm text-slate-700 line-clamp-4">
                                            {{ review.review_text }}
                                        </p>
                                    </div>

                                    <Separator />

                                    <div class="flex items-center justify-between text-xs text-slate-500">
                                        <span>{{ formatDate(review.reviewed_at) }}</span>

                                        <Badge v-if="review.reread" variant="outline" class="text-xs">
                                            Re-read
                                        </Badge>
                                    </div>
                                </div>
                            </Card>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
