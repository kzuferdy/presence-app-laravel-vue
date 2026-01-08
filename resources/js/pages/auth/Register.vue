<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Spinner } from '@/components/ui/spinner'
import { login } from '@/routes'
import { store } from '@/routes/register'
import { Form, Head } from '@inertiajs/vue3'
</script>

<template>
  <Head title="Register" />

  <div class="min-h-screen w-full grid grid-cols-1 lg:grid-cols-2">
     <div class="relative hidden lg:flex flex-col justify-center items-center bg-slate-900 overflow-hidden order-last">
        <div class="absolute inset-0 bg-gradient-to-tl from-indigo-600 to-violet-600 opacity-90"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-white rounded-full mix-blend-overlay filter blur-[100px] opacity-20"></div>

        <div class="relative z-10 p-12 text-white max-w-lg text-center">
             <h1 class="mb-6 text-5xl font-bold tracking-tight leading-tight">
                Join the Club 🚀
            </h1>
             <p class="text-lg text-indigo-100 font-light leading-relaxed">
                Create an account to start using the platform and manage everything
                from one place.
             </p>
        </div>
    </div>

    <div class="flex items-center justify-center bg-slate-50 px-6 py-12">
      <div class="w-full max-w-[480px] bg-white p-10 rounded-3xl shadow-2xl shadow-indigo-100 border border-slate-100">

        <div class="mb-8">
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight">
            Create an account
            </h2>
            <p class="mt-2 text-sm text-slate-500">
            Enter your credentials to continue
            </p>
        </div>

        <Form
          v-bind="store.form()"
          :reset-on-success="['password', 'password_confirmation']"
          v-slot="{ errors, processing }"
          class="space-y-5"
        >
          <div class="space-y-2">
            <Label for="name" class="text-slate-700 font-medium">Full Name</Label>
            <Input
              id="name"
              type="text"
              name="name"
              required
              autofocus
              autocomplete="name"
              placeholder="e.g. Ferdy Mohamad"
              class="h-11 px-4 bg-slate-50 border-slate-200 focus:bg-white transition-all"
            />
            <InputError :message="errors.name" />
          </div>

          <div class="space-y-2">
            <Label for="email" class="text-slate-700 font-medium">Email Address</Label>
            <Input
              id="email"
              type="email"
              name="email"
              required
              autocomplete="email"
              placeholder="name@company.com"
              class="h-11 px-4 bg-slate-50 border-slate-200 focus:bg-white transition-all"
            />
            <InputError :message="errors.email" />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
             <div class="space-y-2">
                <Label for="password" class="text-slate-700 font-medium">Password</Label>
                <Input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="••••••••"
                class="h-11 px-4 bg-slate-50 border-slate-200 focus:bg-white transition-all"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="space-y-2">
                <Label for="password_confirmation" class="text-slate-700 font-medium">Confirm</Label>
                <Input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="••••••••"
                class="h-11 px-4 bg-slate-50 border-slate-200 focus:bg-white transition-all"
                />
                <InputError :message="errors.password_confirmation" />
            </div>
          </div>

          <div class="text-xs text-slate-500 leading-relaxed">
             By creating an account, you agree to our <span class="text-indigo-600 cursor-pointer">Terms of Service</span> and <span class="text-indigo-600 cursor-pointer">Privacy Policy</span>.
          </div>

          <Button
            type="submit"
            class="w-full h-11 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-lg shadow-indigo-200 transition-all active:scale-[0.98]"
            :disabled="processing"
            data-test="register-user-button"
          >
            <Spinner v-if="processing" class="mr-2 h-4 w-4" />
            <span v-else>Create Account</span>
          </Button>
        </Form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center text-sm text-slate-500">
          Already have an account?
          <TextLink :href="login()" class="ml-1 font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">
            Log in
          </TextLink>
        </div>
      </div>
    </div>
  </div>
</template>
