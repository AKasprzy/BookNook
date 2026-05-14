<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

import Button from '@/Components/ui/Button.vue'
import Input from '@/Components/ui/Input.vue'

import { ArrowLeft, User, Lock, Trash2 } from 'lucide-vue-next'

const name = ref('')
const email = ref('')
const loading = ref(false)

const currentPassword = ref('')
const newPassword = ref('')
const newPasswordConfirmation = ref('')

const deleteOpen = ref(false)
const deletePassword = ref('')
const deleteLoading = ref(false)

function goBack() {
    router.visit('/home')
}

async function saveProfile() {
    loading.value = true

    await fetch('/api/user/settings/profile', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify({
            name: name.value,
            email: email.value
        })
    })

    loading.value = false
}

async function changePassword() {
    loading.value = true

    await fetch('/api/user/settings/password', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify({
            current_password: currentPassword.value,
            new_password: newPassword.value,
            new_password_confirmation: newPasswordConfirmation.value
        })
    })

    currentPassword.value = ''
    newPassword.value = ''
    newPasswordConfirmation.value = ''

    loading.value = false
}

function openDelete() {
    deleteOpen.value = true
}

function closeDelete() {
    deleteOpen.value = false
    deletePassword.value = ''
}

async function deleteAccount() {
    deleteLoading.value = true

    await fetch('/api/user/delete', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify({
            password: deletePassword.value
        })
    })

    localStorage.removeItem('token')
    window.location.href = '/'

    deleteLoading.value = false
}

onMounted(async () => {
    try {
        const res = await fetch('/api/user', {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
        })

        if (!res.ok) {
            return
        }

        const json = await res.json()

        name.value = json.name
        email.value = json.email
    } catch (e) {
        name.value = ''
        email.value = ''
    }
})
</script>

<template>
    <div class="min-h-screen bg-slate-50">

        <div class="bg-slate-900 text-white">
            <div class="max-w-4xl mx-auto px-4 py-10">

                <Button variant="ghost" class="text-white mb-4" @click="goBack">
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Back
                </Button>

                <h1 class="text-4xl flex items-center gap-2">
                    <User class="w-8 h-8" />
                    Settings
                </h1>

                <p class="text-slate-300 mt-2">
                    Manage your account
                </p>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 py-10 space-y-8">

            <section class="bg-white p-6 rounded shadow">
                <h2 class="text-xl mb-4 flex items-center gap-2">
                    <User class="w-5 h-5" />
                    Profile
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-slate-600">Name</label>
                        <Input v-model="name" />
                    </div>

                    <div>
                        <label class="text-sm text-slate-600">Email</label>
                        <Input v-model="email" />
                    </div>

                    <Button @click="saveProfile" :disabled="loading">
                        Save changes
                    </Button>
                </div>
            </section>

            <section class="bg-white p-6 rounded shadow">
                <h2 class="text-xl mb-4 flex items-center gap-2">
                    <Lock class="w-5 h-5" />
                    Change Password
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-slate-600">Current password</label>
                        <Input type="password" v-model="currentPassword" />
                    </div>

                    <div>
                        <label class="text-sm text-slate-600">New password</label>
                        <Input type="password" v-model="newPassword" />
                    </div>

                    <div>
                        <label class="text-sm text-slate-600">Confirm new password</label>
                        <Input type="password" v-model="newPasswordConfirmation" />
                    </div>

                    <Button @click="changePassword" :disabled="loading">
                        Update password
                    </Button>
                </div>
            </section>

            <section class="bg-white p-6 rounded shadow border border-red-200">
                <h2 class="text-xl mb-4 flex items-center gap-2 text-red-600">
                    <Trash2 class="w-5 h-5" />
                    Danger Zone
                </h2>

                <Button variant="outline" class="text-red-600 border-red-300" @click="openDelete">
                    Delete account
                </Button>
            </section>

        </div>

        <div v-if="deleteOpen" class="fixed inset-0 bg-black/50 flex items-center justify-center">
            <div class="bg-white p-8 rounded shadow w-full max-w-md space-y-6">

                <h3 class="text-lg font-semibold">Confirm account deletion</h3>

                <p class="text-sm text-slate-600">
                    Enter your password to permanently delete your account.
                </p>

                <Input type="password" v-model="deletePassword" placeholder="Password" />

                <div class="flex justify-end gap-3 pt-2">
                    <Button variant="outline" @click="closeDelete">
                        Cancel
                    </Button>

                    <Button
                        class="bg-red-600 text-white px-6 py-2"
                        :disabled="deleteLoading"
                        @click="deleteAccount"
                    >
                        Delete
                    </Button>
                </div>

            </div>
        </div>

    </div>
</template>
