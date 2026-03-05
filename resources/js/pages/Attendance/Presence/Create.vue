<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import InputError from '@/components/InputError.vue';

interface SchoolClass {
    id: number;
    name: string;
}

interface AcademicYear {
    id: number;
    name: string;
    is_active: boolean;
}

interface Subject {
    id: number;
    name: string;
}

const router = useRouter();
const classes = ref<SchoolClass[]>([]);
const academicYears = ref<AcademicYear[]>([]);
const subjects = ref<Subject[]>([]);

const form = ref({
    class_id: '',
    academic_year_id: '',
    subject_id: '',
    date: new Date().toISOString().split('T')[0],
    errors: {} as Record<string, string>,
});

const fetchData = async () => {
    try {
        const [classesRes, yearsRes, subjectsRes] = await Promise.all([
            axios.get('/api/school-classes'),
            axios.get('/api/academic-years'),
            axios.get('/api/subjects'),
        ]);
        classes.value = classesRes.data;
        academicYears.value = yearsRes.data;
        subjects.value = subjectsRes.data;

        // Set default academic year to active one
        const activeYear = academicYears.value.find(y => y.is_active);
        if (activeYear) {
            form.value.academic_year_id = activeYear.id.toString();
        }
    } catch (error) {
        console.error('Failed to fetch data', error);
    }
};

onMounted(() => {
    fetchData();
});

const scheduleInfo = ref<{ message: string; type: 'info' | 'warning' | 'success' } | null>(null);

const getDayName = (dateStr: string) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
};

import { watch } from 'vue';

watch([() => form.value.class_id, () => form.value.subject_id, () => form.value.date], async ([classId, subjectId, date]) => {
    if (!classId || !date) {
        scheduleInfo.value = null;
        return;
    }

    try {
        const day = getDayName(date);
        const response = await axios.get('/api/schedules', {
            params: {
                filter: {
                    school_class_id: classId,
                    day_of_week: day,
                },
                include: 'subject,teacher',
            }
        });

        const schedules = response.data; // Array of schedules for this class on this day

        if (schedules.length === 0) {
            scheduleInfo.value = {
                message: 'No schedules found for this class today. Attendance is free.',
                type: 'info'
            };
        } else {
            if (subjectId) {
                // Check if selected subject matches any schedule
                const match = schedules.find((s: any) => s.subject_id == subjectId);
                if (match) {
                     scheduleInfo.value = {
                        message: `Scheduled: ${match.start_time.substring(0,5)} - ${match.end_time.substring(0,5)} with ${match.teacher?.name}`,
                        type: 'success'
                    };
                } else {
                     // Get subjects that ARE scheduled
                     const scheduledSubjects = schedules.map((s: any) => s.subject?.name).join(', ');
                     scheduleInfo.value = {
                        message: `Warning: This subject is NOT scheduled for this class today. Scheduled: ${scheduledSubjects}`,
                        type: 'warning'
                    };
                }
            } else {
                 const scheduledSubjects = schedules.map((s: any) => `${s.subject?.name} (${s.start_time.substring(0,5)})`).join(', ');
                 scheduleInfo.value = {
                    message: `Today's Schedule: ${scheduledSubjects}`,
                    type: 'info'
                };
            }
        }
    } catch (error) {
        console.error('Failed to check schedule', error);
    }
});

const submit = () => {
    form.value.errors = {}; // Clear errors
    
    if (!form.value.class_id) {
        form.value.errors.class_id = 'Class is required';
        return;
    }
    if (!form.value.academic_year_id) {
        form.value.errors.academic_year_id = 'Academic Year is required';
        return;
    }
    if (!form.value.subject_id) {
        form.value.errors.subject_id = 'Subject is required';
        return;
    }
    if (!form.value.date) {
        form.value.errors.date = 'Date is required';
        return;
    }

    router.push({
        path: '/attendance/presence', // Fixed: Route is /attendance/presence, not /index
        query: {
            class_id: form.value.class_id,
            academic_year_id: form.value.academic_year_id,
            subject_id: form.value.subject_id,
            date: form.value.date,
        },
    });
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Attendance', href: '/attendance' },
    { title: 'Presence Selection', href: '/attendance/presence/create' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Start Presence</h1>
                <p class="text-sm text-muted-foreground">Select class, subject, and date to start taking attendance.</p>
            </div>

            <div class="rounded-xl border bg-card p-6 shadow-sm max-w-2xl">
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid gap-2">
                        <label for="academic_year" class="text-sm font-medium">Academic Year</label>
                        <select
                            id="academic_year"
                            v-model="form.academic_year_id"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-full"
                            required
                        >
                            <option value="" disabled>Select Academic Year</option>
                            <option v-for="year in academicYears" :key="year.id" :value="year.id">
                                {{ year.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.academic_year_id" class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <label for="class" class="text-sm font-medium">Class</label>
                        <select
                            id="class"
                            v-model="form.class_id"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-full"
                            required
                        >
                            <option value="" disabled>Select Class</option>
                            <option v-for="cls in classes" :key="cls.id" :value="cls.id">
                                {{ cls.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.class_id" class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <label for="subject" class="text-sm font-medium">Subject</label>
                        <select
                            id="subject"
                            v-model="form.subject_id"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-full"
                            required
                        >
                            <option value="" disabled>Select Subject</option>
                            <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
                                {{ subject.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.subject_id" class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <label for="date" class="text-sm font-medium">Date</label>
                        <input
                            id="date"
                            v-model="form.date"
                            type="date"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-full"
                            required
                        />
                        <InputError :message="form.errors.date" class="mt-2" />
                    </div>

                    <div v-if="scheduleInfo" class="rounded-md p-3 text-sm" :class="{
                        'bg-blue-50 text-blue-700': scheduleInfo.type === 'info',
                        'bg-yellow-50 text-yellow-700': scheduleInfo.type === 'warning',
                        'bg-green-50 text-green-700': scheduleInfo.type === 'success',
                    }">
                        {{ scheduleInfo.message }}
                    </div>

                    <div class="pt-4">
                        <button
                            type="submit"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 w-full sm:w-auto"
                        >
                            Start Attendance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
