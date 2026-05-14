<script setup lang="ts">
import { ref } from "vue"
import { router } from "@inertiajs/vue3"
import axios from "@/axios"

import Button from "@/Components/ui/Button.vue"
import Input from "@/Components/ui/Input.vue"
import Label from "@/Components/ui/Label.vue"
import Textarea from "@/Components/ui/Textarea.vue"
import Card from "@/Components/ui/Card.vue"

import {
    BookMarked,
    Upload,
    Plus
} from "lucide-vue-next"

const props = defineProps<{
    bookId: number
}>()

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
    processing: false,
    errors: {} as Record<string, string>
})

async function submit() {
    form.value.processing = true
    form.value.errors = {}

    try {
        const res = await axios.post(`/api/books/${props.bookId}/editions`, {
            edition_title: form.value.edition_title,
            edition_publication_date: form.value.edition_publication_date,
            format: form.value.format,
            edition_language: form.value.edition_language,
            description: form.value.description,
            isbn: form.value.isbn,
            page_count: form.value.page_count,
            length_minutes: form.value.length_minutes,
            cover_url: form.value.cover_url,
            publisher: form.value.publisher
        })

        const editionId = res.data?.id

        if (editionId) {
            router.visit(`/book-editions/${editionId}`)
            return
        }

        router.visit(`/books/${props.bookId}/editions`)
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

            <form @submit.prevent="submit" class="space-y-8">

                <Card class="p-8 shadow-lg border-none">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <BookMarked class="w-6 h-6 text-purple-600" />
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900">Add New Edition</h2>
                            <p class="text-sm text-slate-500">Create a new edition for this book</p>
                        </div>
                    </div>

                    <div class="grid gap-6">

                        <div class="space-y-2">
                            <Label>Edition title *</Label>
                            <Input v-model="form.edition_title" required />
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label>Format *</Label>
                                <select v-model="form.format" class="w-full h-10 border rounded px-3" required>
                                    <option value="" disabled>Select format</option>
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
                                <Input v-model="form.cover_url" class="flex-1" />
                                <Button type="button" variant="outline">
                                    <Upload class="w-4 h-4" />
                                    Upload
                                </Button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label>Description</Label>
                            <Textarea v-model="form.description" rows="5" />
                        </div>

                    </div>
                </Card>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="form.processing" class="bg-slate-900 text-white px-8 gap-2">
                        <Plus v-if="!form.processing" class="w-5 h-5" />
                        {{ form.processing ? 'Creating...' : 'Create Edition' }}
                    </Button>
                </div>

            </form>

        </div>
    </div>
</template>
