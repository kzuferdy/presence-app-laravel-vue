<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'
import { dashboard, login, register } from '@/routes'

defineProps<{
  canRegister: boolean
}>()

const page = usePage()
const user = page.props.auth?.user
</script>

<template>
  <Head title="Welcome" />

  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
    <!-- NAVBAR -->
    <header class="mx-auto flex max-w-7xl items-center justify-between p-6">
      <div class="text-xl font-bold tracking-tight text-slate-800">
        AbsensiKu
      </div>

      <nav class="flex items-center gap-3">
        <Link
          v-if="user"
          :href="dashboard()"
          class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
        >
          Dashboard
        </Link>

        <template v-else>
          <Link
            :href="login()"
            class="rounded-md px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200"
          >
            Login
          </Link>

          <Link
            v-if="canRegister"
            :href="register()"
            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500"
          >
            Register
          </Link>
        </template>
      </nav>
    </header>

    <!-- HERO -->
    <main class="mx-auto max-w-7xl px-6 py-20 text-center">
      <h1
        class="mb-6 text-4xl font-extrabold tracking-tight text-slate-900 md:text-6xl"
      >
        Jangan Tunggu Lagi,
        <span class="text-indigo-600">Gunakan AbsensiKu</span>
      </h1>

      <p class="mx-auto mb-10 max-w-2xl text-lg text-slate-600">
        AbsensiKu memudahkan pengelolaan absensi harian dengan fitur lengkap
        dan antarmuka yang intuitif.
      </p>

      <div class="flex justify-center gap-4">
        <Link
          v-if="user"
          :href="dashboard()"
          class="rounded-lg bg-slate-900 px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800"
        >
          Go to Dashboard
        </Link>

        <template v-else>
          <Link
            :href="login()"
            class="rounded-lg bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-500"
          >
            Ayo Mulai
          </Link>
        </template>
      </div>
    </main>

    <!-- FEATURES -->
    <section class="mx-auto max-w-7xl px-6 pb-24">
      <div class="grid gap-8 md:grid-cols-3">
        <div class="rounded-xl bg-white p-6 shadow-sm">
          <h3 class="mb-2 text-lg font-semibold">⚡ Fast Setup</h3>
          <p class="text-sm text-slate-600">
            Breeze + Inertia + Vue gives you auth, routing, and SSR-ready pages
            out of the box.
          </p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm">
          <h3 class="mb-2 text-lg font-semibold">🧠 Smart State</h3>
          <p class="text-sm text-slate-600">
            Inertia handles page state, Pinia handles UI & global state.
            No duplicate source of truth.
          </p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm">
          <h3 class="mb-2 text-lg font-semibold">🔐 Secure by Default</h3>
          <p class="text-sm text-slate-600">
            Laravel auth, CSRF protection, session-based security.
            Production-grade from day one.
          </p>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="border-t bg-white py-6 text-center text-sm text-slate-500">
      © {{ new Date().getFullYear() }} AbsnesiKu. Built with Laravel and Vue.
    </footer>
  </div>
</template>
