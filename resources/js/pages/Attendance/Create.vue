<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import type { BreadcrumbItemType } from '@/types'

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Attendance', href: '/attendance' },
  { title: 'Add Student' },
]

const form = useForm({
  name: '',
  nis: '',
  class: '',
})
</script>

<template>
  <Head title="Add Student" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="max-w-2xl space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-2xl font-semibold tracking-tight">
          Add Student
        </h1>
        <p class="text-sm text-muted-foreground">
          Fill in the student information below
        </p>
      </div>

      <!-- Form Card -->
      <form
        @submit.prevent="form.post('/attendance/students')"
        class="rounded-xl border bg-card p-6 shadow-sm space-y-6"
      >
        <div class="grid gap-4 sm:grid-cols-2">
          <div class="space-y-1">
            <label class="text-sm font-medium">Student Name</label>
            <input
              v-model="form.name"
              type="text"
              class="w-full rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Full name"
            />
          </div>

          <div class="space-y-1">
            <label class="text-sm font-medium">NIS</label>
            <input
              v-model="form.nis"
              type="text"
              class="w-full rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Student ID"
            />
          </div>

          <div class="space-y-1 sm:col-span-2">
            <label class="text-sm font-medium">Class</label>
            <input
              v-model="form.class"
              type="text"
              class="w-full rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="e.g. 7A"
            />
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-2">
          <Link
            href="/attendance"
            class="rounded-md border px-4 py-2 text-sm"
          >
            Cancel
          </Link>

          <button
            type="submit"
            :disabled="form.processing"
            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
          >
            Save Student
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
