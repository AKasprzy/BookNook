<script setup lang="ts">
import { ref } from 'vue'
import { Eye } from 'lucide-vue-next'

const props = defineProps<{
    reviews: {
        id: number
        rating: number | null
        review_text?: string
        spoiler: boolean
        reread: boolean
        reviewed_at: string
        user: {
            id: number
            name: string
            avatar_url?: string
        }
    }[]
}>()

const emit = defineEmits<{
    (e: 'user-click', userId: number): void
}>()

const revealedSpoilers = ref<Set<number>>(new Set())

function toggleSpoiler(reviewId: number) {
    const newSet = new Set(revealedSpoilers.value)
    if (newSet.has(reviewId)) {
        newSet.delete(reviewId)
    } else {
        newSet.add(reviewId)
    }
    revealedSpoilers.value = newSet
}

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

function renderStars(rating: number | null) {
    if (!rating) return []
    return [1, 2, 3, 4, 5].map((star) => {
        const value = star * 2

        if (rating >= value) return 'full'
        if (rating >= value - 1) return 'half'

        return 'empty'
    })}
</script>

<template>
    <div>
        <div v-if="reviews.length === 0" class="bg-white shadow rounded-xl p-8 text-center">
            <p class="text-slate-500">
                No reviews yet. Be the first to review this book!
            </p>
        </div>

        <div v-else class="space-y-4">
            <h2 class="text-slate-900 font-semibold">Reader Reviews</h2>

            <div
                v-for="review in reviews"
                :key="review.id"
                class="bg-white shadow rounded-xl p-6"
            >
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center overflow-hidden">
                        <img
                            v-if="review.user.avatar_url"
                            :src="review.user.avatar_url"
                            class="w-full h-full object-cover"
                            alt=""
                        />
                        <span v-else class="text-slate-600 font-medium">
                            {{ review.user.name.charAt(0).toUpperCase() }}
                        </span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <p
                                    class="text-slate-900 cursor-pointer hover:text-blue-600 font-medium"
                                    @click="emit('user-click', review.user.id)"
                                >
                                    {{ review.user.name }}
                                </p>

                                <div v-if="review.rating" class="flex gap-0.5">
                                    <span
                                        v-for="(filled, i) in renderStars(review.rating)"
                                        :key="i"
                                        class="text-sm"
                                    >
                                        {{
                                            filled === 'full'
                                                ? '★'
                                                : filled === 'half'
                                                    ? '⯨'
                                                    : '☆'
                                        }}
                                    </span>
                                </div>

                                <span
                                    v-if="review.reread"
                                    class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded"
                                >
                                    Reread
                                </span>
                            </div>

                            <slot name="actions" :review="review"></slot>
                        </div>

                        <p class="text-sm text-slate-500 mb-3">
                            {{ formatDate(review.reviewed_at) }}
                        </p>

                        <div v-if="review.review_text">
                            <div
                                v-if="review.spoiler && !revealedSpoilers.has(review.id)"
                                @click="toggleSpoiler(review.id)"
                                class="bg-slate-50 border border-dashed rounded-lg p-4 cursor-pointer hover:bg-slate-100 transition-colors"
                            >
                                <p class="text-slate-500 text-sm text-center">
                                    This review contains spoilers. Click to reveal.
                                </p>
                            </div>

                            <div v-else>
                                <div
                                    v-if="review.spoiler"
                                    class="text-[10px] uppercase tracking-wider font-bold text-red-500 mb-2"
                                >
                                    Spoiler warning
                                </div>

                                <p class="text-slate-700 leading-relaxed whitespace-pre-line">
                                    {{ review.review_text }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
