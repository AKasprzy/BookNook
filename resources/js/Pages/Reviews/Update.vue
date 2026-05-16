<script setup lang="ts">
import { ref, onMounted } from "vue"
import axios from "@/axios"

import Button from "@/Components/ui/Button.vue"
import Input from "@/Components/ui/Input.vue"
import Label from "@/Components/ui/Label.vue"
import Textarea from "@/Components/ui/Textarea.vue"
import Card from "@/Components/ui/Card.vue"

import { Star, Send } from "lucide-vue-next"

const reviewId = Number(window.location.pathname.split('/').pop())

const form = ref({
    rating: null as number | null,
    review_text: "",
    spoiler: false,
    reread: false,
    processing: false,
    errors: {} as Record<string, string>
})

onMounted(async () => {
    try {
        const { data } = await axios.get(`/api/reviews/${reviewId}`)

        const review = data.data

        form.value.rating = review.rating
        form.value.review_text = review.review_text
        form.value.spoiler = review.spoiler
        form.value.reread = review.reread
    } catch (e) {
        console.error(e)
    }
})

async function submit() {
    form.value.processing = true
    form.value.errors = {}

    try {
        const { data } = await axios.put(`/api/reviews/${reviewId}`, {
            rating: form.value.rating,
            review_text: form.value.review_text,
            spoiler: form.value.spoiler,
            reread: form.value.reread
        })

        const editionId = data.data.book_edition_id
        window.location.href = `/books/${editionId}`
    } catch (e: any) {
        form.value.errors = e.response?.data?.errors || {}
    } finally {
        form.value.processing = false
    }
}
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <div class="max-w-3xl mx-auto px-4 py-12">
            <form @submit.prevent="submit" class="space-y-8">

                <Card class="p-8 shadow-lg border-none">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="p-2 bg-indigo-100 rounded-lg">
                            <Star class="w-6 h-6 text-indigo-600" />
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900">Edit Review</h2>
                            <p class="text-sm text-slate-500">Update your thoughts</p>
                        </div>
                    </div>

                    <div class="space-y-6">

                        <div class="space-y-2">
                            <Label>Rating (1–10)</Label>
                            <Input type="number" v-model.number="form.rating" min="1" max="10" />
                            <p v-if="form.errors.rating" class="text-xs text-rose-500">
                                {{ form.errors.rating }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label>Review</Label>
                            <Textarea v-model="form.review_text" rows="6" />
                            <p v-if="form.errors.review_text" class="text-xs text-rose-500">
                                {{ form.errors.review_text }}
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" v-model="form.spoiler" />
                                Contains spoiler
                            </label>

                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" v-model="form.reread" />
                                Reread
                            </label>
                        </div>

                    </div>
                </Card>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="form.processing" class="bg-slate-900 text-white gap-2 px-8">
                        <Send class="w-5 h-5" />
                        {{ form.processing ? 'Updating...' : 'Update Review' }}
                    </Button>
                </div>

            </form>
        </div>
    </div>
</template>
