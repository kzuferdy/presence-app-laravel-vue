<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    students: {
        id: number;
        name: string;
        nis: string;
        class: string;
        attendances: {
            id: number;
            status: 'present' | 'sick' | 'permission' | 'alpha';
        }[];
    }[];
    date: string;
}>();

const loading = ref<number | null>(null);

const setStatus = (studentId: number, status: string) => {
    loading.value = studentId;
    router.post('/attendance', {
        student_id: studentId,
        date: props.date,
        status: status,
    }, {
        preserveScroll: true,
        onFinish: () => {
            loading.value = null;
        }
    });
};

const getStatus = (student: any) => {
    return student.attendances.length ? student.attendances[0].status : null;
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Attendance', href: '/attendance' },
    { title: 'Presence', href: '/attendance/presence' },
];
</script>

<template>
    <Head title="Presence" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-8">
            <div class="flex flex-col gap-1">
                <h1 class="text-2xl font-semibold tracking-tight">Daily Presence</h1>
                <p class="text-sm text-muted-foreground">Mark attendance for {{ date }}</p>
            </div>

            <div class="rounded-xl border bg-card p-6 shadow-sm">
                <div v-if="students.length" class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="border-b text-left text-muted-foreground">
                                <th class="py-3 px-4">Name</th>
                                <th class="py-3 px-4">NIS</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="student in students"
                                :key="student.id"
                                class="border-b last:border-0 hover:bg-muted/50"
                            >
                                <td class="py-4 px-4 font-medium">{{ student.name }}</td>
                                <td class="py-4 px-4">{{ student.nis }}</td>
                                <td class="py-4 px-4 text-center">
                                    <span v-if="getStatus(student)" 
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                        :class="{
                                            'bg-green-100 text-green-800': getStatus(student) === 'present',
                                            'bg-blue-100 text-blue-800': getStatus(student) === 'sick',
                                            'bg-yellow-100 text-yellow-800': getStatus(student) === 'permission',
                                            'bg-red-100 text-red-800': getStatus(student) === 'alpha',
                                        }"
                                    >
                                        {{ getStatus(student) }}
                                    </span>
                                    <span v-else class="text-muted-foreground">-</span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            @click="setStatus(student.id, 'present')"
                                            class="rounded px-3 py-1.5 text-xs font-medium transition-colors hover:bg-green-200"
                                            :class="getStatus(student) === 'present' ? 'bg-green-200 text-green-900' : 'bg-green-100 text-green-800'"
                                            :disabled="loading === student.id"
                                        >
                                            P
                                        </button>
                                        <button
                                            @click="setStatus(student.id, 'sick')"
                                            class="rounded px-2 py-1 text-xs font-medium transition-colors hover:bg-blue-200"
                                            :class="getStatus(student) === 'sick' ? 'bg-blue-200 text-blue-900' : 'bg-blue-100 text-blue-800'"
                                            :disabled="loading === student.id"
                                        >
                                            S
                                        </button>
                                        <button
                                            @click="setStatus(student.id, 'permission')"
                                            class="rounded px-2 py-1 text-xs font-medium transition-colors hover:bg-yellow-200"
                                            :class="getStatus(student) === 'permission' ? 'bg-yellow-200 text-yellow-900' : 'bg-yellow-100 text-yellow-800'"
                                            :disabled="loading === student.id"
                                        >
                                            I
                                        </button>
                                        <button
                                            @click="setStatus(student.id, 'alpha')"
                                            class="rounded px-2 py-1 text-xs font-medium transition-colors hover:bg-red-200"
                                            :class="getStatus(student) === 'alpha' ? 'bg-red-200 text-red-900' : 'bg-red-100 text-red-800'"
                                            :disabled="loading === student.id"
                                        >
                                            A
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
                        Add students first to start taking attendance.
                    </p>
                    <Link
                         href="/attendance/students"
                        class="mt-4 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                    >
                        Manage Students
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
