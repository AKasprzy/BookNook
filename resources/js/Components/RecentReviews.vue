<script setup lang="ts">
import { ref, onMounted } from "vue"
import { Link } from "@inertiajs/vue3"
import { fetchLatestReviews } from "@/Services/statsService"
import type { Review } from "@/Types/stats"
import { Star, Eye } from "lucide-vue-next"
import Button from "@/Components/ui/Button.vue"
import Badge from "@/Components/ui/Badge.vue"

const reviews = ref<Review[]>([])
const loading = ref(true)
const revealedSpoilers = ref<Set<number>>(new Set())

function toggleSpoiler(id: number) {
    const set = new Set(revealedSpoilers.value)
    set.has(id) ? set.delete(id) : set.add(id)
    revealedSpoilers.value = set
}

function timeAgo(date: string) {
    const now = new Date().getTime()
    const then = new Date(date).getTime()
    const hours = Math.floor((now - then) / 36e5)
    if (hours < 1) return "Just now"
    if (hours < 24) return `${hours}h ago`
    const days = Math.floor(hours / 24)
    if (days < 7) return `${days}d ago`
    return new Date(date).toLocaleDateString("en-US")
}

onMounted(async () => {
    try {
        reviews.value = await fetchLatestReviews(6)
    } finally {
        loading.value = false
    }
})
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-if="loading" class="col-span-full text-center text-gray-500">
            Loading reviews...
        </div>

        <div
            v-for="review in reviews"
            :key="review.id"
            class="bg-white rounded-lg shadow-md p-5 hover:shadow-lg transition-shadow"
        >
            <div class="flex items-start gap-3 mb-4">
                <Link
                    :href="`/users/${review.user.id}`"
                    class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold hover:opacity-90 transition-opacity"
                >
                    {{ review.user.name.charAt(0) }}
                </Link>

                <div class="flex-1 min-w-0">
                    <Link
                        :href="`/users/${review.user.id}`"
                        class="text-sm text-gray-900 truncate hover:underline block"
                    >
                        {{ review.user.name }}
                    </Link>
                    <p class="text-xs text-gray-500">
                        {{ timeAgo(review.created_at) }}
                    </p>
                </div>
            </div>

            <div class="mb-3">
                <h4 class="text-gray-900 mb-1 line-clamp-1">
                    {{ review.book.title }}
                </h4>

                <div class="flex items-center gap-2">
                    <div class="flex gap-1">
                        <span
                            v-for="i in 5"
                            :key="i"
                            class="text-sm"
                            :class="{
                                'text-gray-300': i > Math.ceil((review.rating ?? 0) / 2)
                            }"
                        >
                            {{
                                i <= Math.floor((review.rating ?? 0) / 2)
                                    ? '★'
                                    : i === Math.ceil((review.rating ?? 0) / 2) && (review.rating ?? 0) % 2 !== 0
                                        ? '⯨'
                                        : '☆'
                            }}
                        </span>
                    </div>

                    <div class="flex gap-1">
                        <Badge v-if="review.reread" variant="secondary" class="text-xs">
                            Reread
                        </Badge>
                        <Badge v-if="review.spoiler" variant="destructive" class="text-xs gap-1">
                            <Eye class="w-3 h-3" />
                            Spoiler
                        </Badge>
                    </div>
                </div>
            </div>

            <div
                v-if="review.spoiler && !revealedSpoilers.has(review.id)"
                class="bg-gray-100 border border-gray-200 rounded-lg p-4 text-center"
            >
                <Eye class="w-5 h-5 text-gray-500 mx-auto mb-2" />
                <p class="text-sm text-gray-600 mb-3">
                    This review contains spoilers
                </p>
                <Button size="sm" variant="outline" @click="toggleSpoiler(review.id)">
                    Show spoiler
                </Button>
            </div>

            <div v-else>
                <p class="text-sm text-gray-700 line-clamp-4">
                    {{ review.review_text }}
                </p>
                <Button
                    v-if="review.spoiler"
                    size="sm"
                    variant="ghost"
                    class="mt-2 text-xs"
                    @click="toggleSpoiler(review.id)"
                >
                    Hide spoiler
                </Button>
            </div>
        </div>
    </div>
</template>
