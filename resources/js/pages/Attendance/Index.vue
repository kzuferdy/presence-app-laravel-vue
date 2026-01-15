<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import type { BreadcrumbItemType } from '@/types'

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Attendance', href: '/attendance' },
]

const props = withDefaults(
  defineProps<{
    students?: {
      id: number
      name: string
      nis: string
      class: string
    }[]
  }>(),
  {
    students: () => [],
  }
)
</script>


<template>
  <Head title="Attendance" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6">
      <!-- Header -->
      <div
        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
      >
        <div>
          <h1 class="text-2xl font-semibold tracking-tight">
            Attendance
          </h1>
          <p class="text-sm text-muted-foreground">
            Manage students and attendance data
          </p>
        </div>

        <Link
          href="/attendance/students/create"
          class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
        >
          + Add Student
        </Link>
      </div>

      <!-- Content -->
      <div class="rounded-xl border bg-card p-6 shadow-sm">
        <!-- TABLE -->
        <div v-if="props.students.length" class="overflow-x-auto">
          <table class="w-full border-collapse text-sm">
            <thead>
              <tr class="border-b text-left text-muted-foreground">
                <th class="py-2">Name</th>
                <th class="py-2">NIS</th>
                <th class="py-2">Class</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="student in students"
                :key="student.id"
                class="border-b last:border-0"
              >
                <td class="py-2 font-medium">
                  {{ student.name }}
                </td>
                <td class="py-2">
                  {{ student.nis }}
                </td>
                <td class="py-2">
                  {{ student.class }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- EMPTY STATE -->
        <div
          v-else
          class="flex flex-col items-center justify-center gap-2 py-16 text-center"
        >
          <div class="text-4xl">📋</div>
          <h3 class="text-lg font-medium">
            No students yet
          </h3>
          <p class="max-w-sm text-sm text-muted-foreground">
            You haven’t added any students to the attendance module.
            Start by adding your first student.
          </p>

          <Link
            href="/attendance/students/create"
            class="mt-4 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
          >
            Add first student
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
