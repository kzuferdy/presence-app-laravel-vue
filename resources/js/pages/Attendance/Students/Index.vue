<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    students: {
        id: number;
        name: string;
        nis: string;
        class: string;
        photo: string | null;
        address: string | null;
        school_class?: {
            id: number;
            name: string;
        } | null;
    }[];
}>();

const deleteStudent = (id: number) => {
    if (confirm('Are you sure you want to delete this student?')) {
        router.delete(`/attendance/students/${id}`);
    }
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Attendance', href: '/attendance' },
    { title: 'Data Siswa', href: '/attendance/students' },
];
</script>

<template>
    <Head title="Data Siswa" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Data Siswa</h1>
                    <p class="text-sm text-muted-foreground">Manage student data here.</p>
                </div>

                <Link
                    href="/attendance/students/create"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    + Add Student
                </Link>
            </div>

            <div class="rounded-xl border bg-card p-6 shadow-sm">
                <div v-if="props.students.length" class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="border-b text-left text-muted-foreground">
                                <th class="py-3 px-4">Photo</th>
                                <th class="py-3 px-4">Name</th>
                                <th class="py-3 px-4">NIS</th>
                                <th class="py-3 px-4">Class</th>
                                <th class="py-3 px-4">Address</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="student in students"
                                :key="student.id"
                                class="border-b last:border-0 hover:bg-muted/50"
                            >
                                <td class="py-4 px-4">
                                    <div class="h-10 w-10 overflow-hidden rounded-full bg-muted">
                                        <img
                                            v-if="student.photo"
                                            :src="`/storage/${student.photo}`"
                                            alt="Student Photo"
                                            class="h-full w-full object-cover"
                                        />
                                        <div v-else class="flex h-full w-full items-center justify-center text-xs text-muted-foreground">
                                            No Photo
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 font-medium">{{ student.name }}</td>
                                <td class="py-4 px-4">{{ student.nis }}</td>
                                <td class="py-4 px-4">{{ student.school_class?.name || student.class }}</td>
                                <td class="py-4 px-4 text-muted-foreground">{{ student.address || '-' }}</td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link
                                            :href="`/attendance/students/${student.id}/edit`"
                                            class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            @click="deleteStudent(student.id)"
                                            class="text-sm font-medium text-red-600 hover:text-red-500"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class="flex flex-col items-center justify-center gap-2 py-16 text-center">
                    <div class="text-4xl">📋</div>
                    <h3 class="text-lg font-medium">No students yet</h3>
                    <p class="max-w-sm text-sm text-muted-foreground">
                        You haven’t added any students yet. Start by adding your first student.
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
