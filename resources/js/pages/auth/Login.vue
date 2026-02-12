<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'
import AuthLayout from '@/layouts/AuthLayout.vue'
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import axios from 'axios'
import { useAuth } from '@/composables/useAuth'

defineProps<{
    status?: string
}>()

const form = ref({
    email: '',
    password: '',
    remember: false,
    processing: false,
    errors: {} as Record<string, string>,
})

const router = useRouter()
const { fetchUser } = useAuth()

const submit = async () => {
    form.value.processing = true
    form.value.errors = {}

    try {
        const response = await axios.post('/api/login', {
            email: form.value.email,
            password: form.value.password,
            remember: form.value.remember,
        })
        
        localStorage.setItem('token', response.data.token)
        await fetchUser()
        router.push('/dashboard')
    } catch (error: any) {
        if (error.response && error.response.data.errors) {
            form.value.errors = error.response.data.errors
        } else {
             console.error(error)
        }
    } finally {
        form.value.processing = false
    }
}
</script>

<template>
  <div class="min-h-screen w-full grid grid-cols-1 lg:grid-cols-2">
    <!-- Left Side: Decorative & Info -->
    <div class="relative hidden lg:flex flex-col justify-center items-center bg-slate-900 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-violet-600 to-indigo-600 opacity-90"></div>
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>

        <div class="relative z-10 p-12 text-white max-w-lg text-center">
            <h1 class="mb-6 text-5xl font-bold tracking-tight leading-tight">
                Welcome back! 👋
            </h1>
            <p class="text-lg text-indigo-100 font-light leading-relaxed">
                Sign in to access your dashboard, manage data, and continue building
                with our Laravel + Vue monorepo.
            </p>
        </div>
    </div>

    <!-- Right Side: Login Form -->
    <div class="flex items-center justify-center bg-slate-50 px-6 py-12">
      <div class="w-full max-w-[420px] bg-white p-10 rounded-3xl shadow-2xl shadow-indigo-100 border border-slate-100">

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
            Log in to your account
            </h2>
            <p class="mt-2 text-sm text-slate-500">
            Enter your credentials to continue
            </p>
        </div>

        <div v-if="status" class="mb-6 rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-600 border border-emerald-100">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <Label for="email" class="text-slate-700">Email address</Label>
                <Input
                    id="email"
                    type="email"
                    class="mt-2 block w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="name@example.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <Label for="password" class="text-slate-700">Password</Label>
                <Input
                    id="password"
                    type="password"
                    class="mt-2 block w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <Checkbox id="remember" :checked="form.remember" @update:checked="form.remember = $event" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                    <span class="ms-2 text-sm text-slate-600">Remember me</span>
                </label>

                <RouterLink
                    to="/forgot-password"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                >
                    Forgot password?
                </RouterLink>
            </div>

            <Button
                class="w-full py-6 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg shadow-indigo-200 transition-all duration-200"
                :class="{ 'opacity-70 cursor-not-allowed': form.processing }"
                :disabled="form.processing"
            >
                Sign in
            </Button>
            
             <div class="text-center text-sm text-slate-500 mt-6">
                Don't have an account?
                <RouterLink
                    to="/register"
                    class="font-medium text-indigo-600 hover:text-indigo-500 hover:underline"
                >
                    Create an account
                </RouterLink>
            </div>
        </form>
      </div>
    </div>
  </div>
</template>
