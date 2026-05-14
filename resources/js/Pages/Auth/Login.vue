<script setup lang="ts">
import { reactive } from "vue"
import axios from "@/axios"
import { Link } from "@inertiajs/vue3"
import { Mail, Lock } from "lucide-vue-next"

import Button from "@/Components/ui/Button.vue"
import Input from "@/Components/ui/Input.vue"
import Label from "@/Components/ui/Label.vue"
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/Components/ui/Card.vue"

const form = reactive({
    email: "",
    password: "",
    errors: {} as Record<string, string>,
    processing: false,
})

async function submit() {
    form.processing = true
    form.errors = {}

    try {
        const response = await axios.post("/api/auth/login", {
            email: form.email,
            password: form.password,
        })

        localStorage.setItem("token", response.data.token)

        axios.defaults.headers.common["Authorization"] = `Bearer ${response.data.token}`

        window.location.href = "/home"
    } catch (error: any) {
        form.errors.email = error.response?.data?.message || "Login failed"
    } finally {
        form.processing = false
    }
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-slate-50">
        <div class="w-full max-w-md">
            <Card class="shadow-xl border-none">
                <CardHeader>
                    <CardTitle>Login</CardTitle>
                    <CardDescription>Access your account</CardDescription>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label>Email</Label>
                            <div class="relative">
                                <Mail class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                                <Input v-model="form.email" class="pl-10 h-10" type="email" required />
                            </div>
                            <p v-if="form.errors.email" class="text-xs text-red-500">{{ form.errors.email }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label>Password</Label>
                            <div class="relative">
                                <Lock class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                                <Input v-model="form.password" class="pl-10 h-10" type="password" required />
                            </div>
                            <p v-if="form.errors.password" class="text-xs text-red-500">{{ form.errors.password }}</p>
                        </div>

                        <Button type="submit" class="w-full" :disabled="form.processing">
                            {{ form.processing ? "Logging in..." : "Login" }}
                        </Button>

                        <p class="text-sm text-center text-slate-600">
                            Don’t have an account?
                            <Link href="/register" class="text-slate-900 font-medium">Sign up</Link>
                        </p>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
