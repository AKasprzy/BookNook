    <script setup lang="ts">
    import { ref, onMounted, computed } from 'vue'
    import { router } from '@inertiajs/vue3'
    import BookEditionHero from '@/Components/BookEditionHero.vue'
    import GenresAndMotifs from '@/Components/GenresAndMotifs.vue'
    import EditionDetails from '@/Components/EditionDetails.vue'
    import ReviewsSection from '@/Components/ReviewsSection.vue'
    import BookStats from '@/Components/BookStats.vue'
    import OriginalBookInfo from '@/Components/OriginalBookInfo.vue'
    import Button from '@/Components/ui/Button.vue'
    import { BookOpen, Heart, CheckCircle2, BookMarked, BookX, Trash2, Settings, ChevronDown } from 'lucide-vue-next'
    import axios from '@/axios'

    import type { BookEdition } from '@/Types/edition'

    type Status = 'read' | 'reading' | 'tbr' | 'dnf'

    interface Shelf {
        id: number
        user_id: number
        book_id: number
        status: Status
        times_read: number
        favourite: boolean
        notes?: string
    }

    interface Review {
        id: number
        user_id: number
        book_edition_id: number
        rating: number
        review_text: string
    }

    const pathParts = window.location.pathname.split('/').filter(Boolean)
    const editionId = Number(pathParts[pathParts.length - 1])

    const edition = ref<BookEdition | null>(null)
    const genres = ref<any[]>([])
    const motifs = ref<any[]>([])
    const reviews = ref<any[]>([])
    const myReview = ref<Review | null>(null)
    const shelf = ref<Shelf | null>(null)
    const loggedIn = ref(false)
    const loading = ref(true)
    const notes = ref('')
    const timesRead = ref<number>(0)
    const userRole = ref<string | null>(null)
    const token = localStorage.getItem('token')
    const showModMenu = ref(false)

    const isModerator = computed(() =>
        ['moderator', 'admin', 'superAdmin'].includes(userRole.value || '')
    )

    const isAdmin = computed(() =>
        ['admin', 'superAdmin'].includes(userRole.value || '')
    )

    const isSuperAdmin = computed(() =>
        userRole.value === 'superAdmin'
    )

    const statuses: { value: Status; label: string; icon: any; color: string }[] = [
        { value: 'read', label: 'Read', icon: CheckCircle2, color: 'bg-green-500 hover:bg-green-600' },
        { value: 'reading', label: 'Reading', icon: BookOpen, color: 'bg-blue-500 hover:bg-blue-600' },
        { value: 'tbr', label: 'Want to Read', icon: BookMarked, color: 'bg-amber-500 hover:bg-amber-600' },
        { value: 'dnf', label: 'Did Not Finish', icon: BookX, color: 'bg-slate-500 hover:bg-slate-600' },
    ]

    const currentStatus = computed(() => {
        if (!shelf.value) return null
        return statuses.find(s => s.value === shelf.value!.status) || null
    })

    function addEdition() {
        if (!edition.value?.book?.id) return
        router.visit(`/book-editions/create/${edition.value.book.id}`)
    }

    function browseEditions() {
        if (!edition.value?.book?.id) return
        router.visit(`/books/${edition.value.book.id}/editions`)
    }

    function handleUserClick(userId: number) {
        router.visit(`/users/${userId}`)
    }

    function goToReview() {
        if (!loggedIn.value) {
            router.visit('/login')
            return
        }
        router.visit(`/reviews/create/${editionId}`)
    }

    function editMyReview() {
        if (!myReview.value) return
        router.visit(`/reviews/edit/${myReview.value.id}`)
    }

    function editEdition() {
        if (!edition.value) return
        router.visit(`/book-editions/edit/${edition.value.id}`)
    }

    async function handleDeleteBook(force: boolean = false) {
        const bookId = edition.value?.book?.id
        if (!bookId) return

        const confirmMsg = force ? 'Permanently delete this entire book and all its editions?' : 'Delete this book?'
        if (!confirm(confirmMsg)) return

        try {
            const url = force ? `/api/books/${bookId}/force` : `/api/books/${bookId}`
            await axios.delete(url, {
                headers: { Authorization: `Bearer ${token}` }
            })
            router.visit('/')
        } catch (e) {
            console.error(e)
        }
    }

    async function deleteReview(reviewId: number, force: boolean = false) {
        const confirmMsg = force ? 'Permanently delete this review?' : 'Delete this review?'
        if (!confirm(confirmMsg)) return
        try {
            const url = force ? `/api/reviews/${reviewId}/force` : `/api/reviews/${reviewId}`
            await axios.delete(url, {
                headers: { Authorization: `Bearer ${token}` }
            })
            reviews.value = reviews.value.filter(r => r.id !== reviewId)
        } catch (e) {
            console.error(e)
        }
    }

    async function updateStatus(status: Status) {
        if (!loggedIn.value) {
            router.visit('/login')
            return
        }
        try {
            const { data } = await axios.post('/api/shelves',
                { book_edition_id: editionId, status },
                { headers: { Authorization: `Bearer ${token}` } }
            )
            shelf.value = data.data ?? data
            notes.value = shelf.value?.notes ?? ''
            timesRead.value = shelf.value?.times_read ?? 0
        } catch (e) {
            console.error(e)
        }
    }

    async function toggleFavourite() {
        if (!shelf.value) return
        try {
            const { data } = await axios.put(`/api/shelves/${shelf.value.id}`,
                { favourite: !shelf.value.favourite, notes: notes.value, times_read: timesRead.value },
                { headers: { Authorization: `Bearer ${token}` } }
            )
            shelf.value = data.data ?? data
        } catch (e) {
            console.error(e)
        }
    }

    async function saveShelfMeta() {
        if (!shelf.value || !token) return
        try {
            const { data } = await axios.put(`/api/shelves/${shelf.value.id}`,
                { favourite: shelf.value.favourite, notes: notes.value, times_read: timesRead.value, status: shelf.value.status },
                { headers: { Authorization: `Bearer ${token}` } }
            )
            shelf.value = data.data ?? data
        } catch (e) {
            console.error(e)
        }
    }

    onMounted(async () => {
        try {
            const editionRes = await fetch(`/api/book-editions/${editionId}`, {
                headers: token ? { Authorization: `Bearer ${token}` } : {}
            })
            const editionJson = await editionRes.json()
            if (!editionJson?.data) return
            edition.value = editionJson.data
            genres.value = edition.value?.genres ?? []
            motifs.value = edition.value?.motifs ?? []

            const reviewsRes = await fetch(`/api/reviews?book_edition_id=${editionId}`)
            const reviewsJson = await reviewsRes.json()
            reviews.value = (reviewsJson.data ?? []).filter((r: any) => r.book_edition_id === editionId)

            if (token) {
                loggedIn.value = true
                const meRes = await fetch('/api/user', { headers: { Authorization: `Bearer ${token}` } })
                const meJson = await meRes.json()
                userRole.value = meJson.role

                const [shelfRes, myRevRes] = await Promise.all([
                    fetch('/api/my-shelves', { headers: { Authorization: `Bearer ${token}` } }),
                    fetch('/api/my-reviews', { headers: { Authorization: `Bearer ${token}` } })
                ])
                const shelfJson = await shelfRes.json()
                const myRevJson = await myRevRes.json()

                shelf.value = shelfJson.data?.find((s: any) => s.book_edition_id === editionId) ?? null
                if (shelf.value) {
                    notes.value = shelf.value.notes ?? ''
                    timesRead.value = shelf.value.times_read ?? 0
                }
                myReview.value = myRevJson.data?.find((r: any) => r.book_edition_id === editionId) ?? null
            }
        } catch (e) {
            console.error(e)
        } finally {
            loading.value = false
        }
    })
    </script>

    <template>
        <div v-if="!loading && edition" class="min-h-screen bg-slate-50">
            <div class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">
                <div class="relative max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center mb-6">
                        <Button variant="ghost" class="text-white hover:bg-white/10 px-6 py-2" @click="router.visit('/')">
                            ← Back
                        </Button>

                        <div v-if="isModerator" class="relative">
                            <Button
                                class="bg-white/10 hover:bg-white/20 text-white gap-2 border border-white/20"
                                @click="showModMenu = !showModMenu"
                            >
                                <Settings class="w-4 h-4" />
                                Manage
                                <ChevronDown class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showModMenu }" />
                            </Button>

                            <div v-if="showModMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border py-1 z-50 overflow-hidden">
                                <button @click="editEdition" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 flex items-center gap-2">
                                    <Settings class="w-4 h-4" /> Edit Book
                                </button>
                                <button v-if="isAdmin" @click="handleDeleteBook(false)" class="w-full text-left px-4 py-2 text-sm text-orange-600 hover:bg-orange-50 flex items-center gap-2 font-medium">
                                    <Trash2 class="w-4 h-4" /> Hide Book
                                </button>
                                <button v-if="isSuperAdmin" @click="handleDeleteBook(true)" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 font-bold border-t">
                                    <Trash2 class="w-4 h-4" /> Delete Book
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <BookEditionHero :edition="edition" />
                    </div>

                    <div class="mt-6 flex gap-3">
                        <Button @click="addEdition" class="bg-white text-slate-900 hover:bg-slate-100 px-4 py-2">
                            Add Edition
                        </Button>
                        <Button @click="browseEditions" class="bg-slate-700 text-white hover:bg-slate-600 px-4 py-2">
                            Browse Editions
                        </Button>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-8">
                        <GenresAndMotifs :genres="genres" :motifs="motifs" />
                        <EditionDetails :edition="edition" />

                        <div v-if="myReview" class="bg-white rounded-xl border shadow p-6">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center font-bold">Y</div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <p class="font-bold">Your Review</p>
                                        <div class="flex text-amber-400">
                                            <span v-for="i in 5" :key="i">
                                                {{
                                                    i <= Math.floor(myReview.rating / 2)
                                                        ? '★'
                                                        : i === Math.ceil(myReview.rating / 2) && myReview.rating % 2 !== 0
                                                            ? '⯨'
                                                            : '☆'
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-slate-700 whitespace-pre-line">{{ myReview.review_text }}</p>
                                    <Button class="mt-4" variant="outline" size="sm" @click="editMyReview">Edit Review</Button>
                                </div>
                            </div>
                        </div>

                        <ReviewsSection :reviews="reviews" @user-click="handleUserClick">
                            <template #actions="{ review }">
                                <div v-if="isModerator" class="flex gap-1">
                                    <Button variant="ghost" class="text-orange-600 hover:bg-orange-50 p-2 h-auto" title="Delete Review" @click="deleteReview(review.id, false)">
                                        <Trash2 class="w-4 h-4" />
                                    </Button>
                                    <Button v-if="isSuperAdmin" variant="ghost" class="text-red-600 hover:bg-red-50 p-2 h-auto" title="Force Delete Review" @click="deleteReview(review.id, true)">
                                        <Trash2 class="w-4 h-4" />
                                    </Button>
                                </div>
                            </template>
                        </ReviewsSection>
                    </div>

                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white rounded-xl border shadow p-6 space-y-4">
                            <h3 class="text-lg font-bold">Shelf Management</h3>
                            <div v-if="shelf && currentStatus" class="p-4 bg-slate-50 rounded-lg border space-y-4">
                                <div class="flex items-center gap-2 text-indigo-700 font-bold">
                                    <component :is="currentStatus.icon" class="w-5 h-5" />
                                    {{ currentStatus.label }}
                                </div>
                                <textarea v-model="notes" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" rows="3" placeholder="Notes..."></textarea>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium">Times Read</span>
                                    <select v-model="timesRead" class="border rounded px-2 py-1 text-sm bg-white">
                                        <option v-for="n in 11" :key="n-1" :value="n-1">{{ n-1 }}</option>
                                    </select>
                                </div>
                                <div class="flex gap-2">
                                    <Button variant="outline" class="flex-1" @click="shelf = null">Change</Button>
                                    <Button class="flex-1 bg-indigo-600 text-white" @click="saveShelfMeta">Save</Button>
                                </div>
                            </div>
                            <div v-else class="grid grid-cols-2 gap-2">
                                <Button v-for="status in statuses" :key="status.value" :class="status.color + ' text-white py-6 flex-col gap-1'" size="sm" @click="updateStatus(status.value)">
                                    <component :is="status.icon" class="w-5 h-5" />
                                    <span class="text-[10px] uppercase font-bold">{{ status.label }}</span>
                                </Button>
                            </div>
                            <Button v-if="shelf" :variant="shelf.favourite ? 'default' : 'outline'" class="w-full gap-2" @click="toggleFavourite">
                                <Heart class="w-4 h-4" :class="shelf.favourite ? 'fill-current text-red-500' : ''" />
                                {{ shelf.favourite ? 'In Favourites' : 'Add to Favourites' }}
                            </Button>
                            <Button v-if="loggedIn" @click="goToReview" class="w-full bg-indigo-500 text-white">Write Review</Button>
                        </div>
                        <BookStats v-if="edition" :editionId="edition.id" :averageRating="edition.average_rating ?? 0" :totalReviews="reviews.length" />
                        <OriginalBookInfo v-if="edition.book" :book="edition.book" />
                    </div>
                </div>
            </div>
        </div>
    </template>
