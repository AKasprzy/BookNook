<script setup lang="ts">
defineProps<{
    edition: any
}>()
</script>

<template>
    <div class="relative bg-gradient-to-b from-slate-900 to-slate-800">
        <div class="absolute inset-0 opacity-20">
            <img
                :src="edition.cover_url || ''"
                alt=""
                class="w-full h-full object-cover blur-2xl"
            />
        </div>

        <div class="relative max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-6 items-start">

                <div class="flex-shrink-0">
                    <div class="w-40 md:w-48 shadow-2xl rounded-lg overflow-hidden">
                        <img
                            :src="edition.cover_url || ''"
                            :alt="edition.edition_title"
                            class="w-full h-auto"
                        />
                    </div>
                </div>

                <div class="flex-1 text-white">
                    <div class="space-y-3">

                        <p
                            v-if="edition.book?.series"
                            class="text-slate-300 text-sm uppercase tracking-wide"
                        >
                            {{ edition.book.series }}
                        </p>

                        <h1 class="text-white text-3xl md:text-4xl">
                            {{ edition.edition_title }}
                        </h1>

                        <p class="text-slate-300 text-lg">
                            by {{ edition.book?.author }}
                        </p>

                        <div class="flex flex-wrap gap-2">
              <span class="bg-slate-700 text-white px-2 py-1 rounded">
                {{ edition.format }}
              </span>

                            <span
                                v-if="edition.edition_publication_date"
                                class="bg-slate-700 text-white px-2 py-1 rounded"
                            >
                {{ new Date(edition.edition_publication_date).getFullYear() }}
              </span>

                            <span class="bg-slate-700 text-white px-2 py-1 rounded">
                {{ edition.edition_language }}
              </span>
                        </div>

                        <p
                            v-if="edition.description"
                            class="text-slate-300 text-sm leading-relaxed max-w-3xl line-clamp-3"
                        >
                            {{ edition.description }}
                        </p>

                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
