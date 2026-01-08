<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Spinner } from '@/components/ui/spinner'
import { register } from '@/routes'
import { store } from '@/routes/login'
import { request } from '@/routes/password'
import { Form, Head } from '@inertiajs/vue3'

defineProps<{
  status?: string
  canResetPassword: boolean
  canRegister: boolean
}>()
</script>

<template>
  <Head title="Log in" />

  <div class="min-h-screen w-full grid grid-cols-1 lg:grid-cols-2">
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

        <div
          v-if="status"
          class="mb-6 rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-600 border border-emerald-100"
        >
          {{ status }}
        </div>

        <Form
          v-bind="store.form()"
          :reset-on-success="['password']"
          v-slot="{ errors, processing }"
          class="space-y-6"
        >
          <div class="space-y-2">
            <Label for="email" class="text-slate-700 font-medium">Email Address</Label>
            <Input
              id="email"
              type="email"
              name="email"
              required
              autofocus
              autocomplete="email"
              placeholder="name@company.com"
              class="h-11 px-4 bg-slate-50 border-slate-200 focus:bg-white transition-all"
            />
            <InputError :message="errors.email" />
          </div>

          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <Label for="password" class="text-slate-700 font-medium">Password</Label>
              <TextLink
                v-if="canResetPassword"
                :href="request()"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors"
              >
                Forgot password?
              </TextLink>
            </div>

            <Input
              id="password"
              type="password"
              name="password"
              required
              autocomplete="current-password"
              placeholder="••••••••"
              class="h-11 px-4 bg-slate-50 border-slate-200 focus:bg-white transition-all"
            />
            <InputError :message="errors.password" />
          </div>

          <div class="flex items-center">
             <div class="flex items-center space-x-2">
                <Checkbox id="remember" name="remember" class="border-slate-300 data-[state=checked]:bg-indigo-600 data-[state=checked]:border-indigo-600" />
                <Label
                    for="remember"
                    class="text-sm font-medium text-slate-600 cursor-pointer select-none"
                >
                    Remember me for 30 days
                </Label>
             </div>
          </div>

          <Button
            type="submit"
            class="w-full h-11 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-lg shadow-indigo-200 transition-all active:scale-[0.98]"
            :disabled="processing"
            data-test="login-button"
          >
            <Spinner v-if="processing" class="mr-2 h-4 w-4" />
            <span v-else>Sign In</span>
          </Button>
        </Form>

        <div
          v-if="canRegister"
          class="mt-8 pt-6 border-t border-slate-100 text-center text-sm text-slate-500"
        >
          Don’t have an account?
          <TextLink :href="register()" class="ml-1 font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">
            Sign up now
          </TextLink>
        </div>
      </div>
    </div>
  </div>
</template>
