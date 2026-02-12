<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { format } from 'date-fns';

const props = defineProps<{
    students: {
        id: number;
        name: string;
        nis: string;
        attendances: {
            id: number;
            date: string;
            status: 'present' | 'sick' | 'permission' | 'alpha';
        }[];
    }[];
    filters: {
        month: number;
        year: number;
    };
}>();

const months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

const selectedMonth = ref(props.filters.month);
const selectedYear = ref(props.filters.year);

const updateFilters = () => {
    router.get('/attendance/recap', {
        month: selectedMonth.value,
        year: selectedYear.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const getDaysInMonth = (month: number, year: number) => {
    return new Date(year, month, 0).getDate();
};

const days = ref<number[]>([]);

watch([selectedMonth, selectedYear], () => {
    const daysCount = getDaysInMonth(selectedMonth.value, selectedYear.value);
    days.value = Array.from({ length: daysCount }, (_, i) => i + 1);
}, { immediate: true });

const getStatus = (student: any, day: number) => {
    const date = `${selectedYear.value}-${String(selectedMonth.value).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    const attendance = student.attendances.find((a: any) => a.date === date);
    return attendance ? attendance.status.charAt(0).toUpperCase() : '-';
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'P': return 'bg-green-100 text-green-800';
        case 'S': return 'bg-blue-100 text-blue-800';
        case 'I': return 'bg-yellow-100 text-yellow-800';
        case 'A': return 'bg-red-100 text-red-800';
        default: return 'text-muted-foreground';
    }
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Attendance', href: '/attendance' },
    { title: 'Rekap Absen', href: '/attendance/recap' },
];
</script>

<template>
    <Head title="Rekap Absen" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Rekap Absen</h1>
                    <p class="text-sm text-muted-foreground">Monthly attendance recap.</p>
                </div>

                <div class="flex items-center gap-2">
                    <select
                        v-model="selectedMonth"
                        @change="updateFilters"
                        class="rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    >
                        <option v-for="(month, index) in months" :key="index" :value="index + 1">
                            {{ month }}
                        </option>
                    </select>
                    <select
                        v-model="selectedYear"
                        @change="updateFilters"
                        class="rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    >
                        <option v-for="year in [2024, 2025, 2026, 2027]" :key="year" :value="year">
                            {{ year }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="rounded-xl border bg-card shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="border-b bg-muted/50 text-left text-muted-foreground">
                                <th class="py-3 px-4 whitespace-nowrap sticky left-0 bg-background z-10">Name</th>
                                <th v-for="day in days" :key="day" class="py-3 px-2 text-center min-w-[2rem]">
                                    {{ day }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="student in students"
                                :key="student.id"
                                class="border-b last:border-0 hover:bg-muted/50"
                            >
                                <td class="py-3 px-4 font-medium whitespace-nowrap sticky left-0 bg-background z-10 border-r">
                                    {{ student.name }}
                                </td>
                                <td v-for="day in days" :key="day" class="py-3 px-2 text-center">
                                    <span
                                        class="inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-medium"
                                        :class="getStatusColor(getStatus(student, day))"
                                    >
                                        {{ getStatus(student, day) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div v-if="!students.length" class="p-8 text-center text-muted-foreground">
                    No students found.
                </div>
            </div>

            <div class="flex gap-4 text-sm text-muted-foreground">
                <div class="flex items-center gap-1"><span class="h-3 w-3 rounded-full bg-green-100"></span> P = Present</div>
                <div class="flex items-center gap-1"><span class="h-3 w-3 rounded-full bg-blue-100"></span> S = Sick</div>
                <div class="flex items-center gap-1"><span class="h-3 w-3 rounded-full bg-yellow-100"></span> I = Permission</div>
                <div class="flex items-center gap-1"><span class="h-3 w-3 rounded-full bg-red-100"></span> A = Alpha</div>
            </div>
        </div>
    </AppLayout>
</template>
