<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Plus, Pencil, Trash2, Search, Check, ChevronDown, Clock } from 'lucide-vue-next';

type Schedule = {
    id: number;
    school_class_id: number;
    subject_id: number;
    user_id: number;
    day_of_week: string;
    start_time: string;
    end_time: string;
    class?: { name: string };
    subject?: { name: string };
    teacher?: { name: string };
};

type SchoolClass = {
    id: number;
    name: string;
};

type Subject = {
    id: number;
    name: string;
};

type Teacher = {
    id: number;
    name: string;
};

const schedules = ref<Schedule[]>([]);
const classes = ref<SchoolClass[]>([]);
const subjects = ref<Subject[]>([]);
const teachers = ref<Teacher[]>([]);
const loading = ref(false);

const isDialogOpen = ref(false);
const isEditing = ref(false);
const currentId = ref<number | null>(null);

const form = ref({
    school_class_id: '',
    subject_id: '',
    user_id: '',
    day_of_week: 'monday',
    start_time: '',
    end_time: '',
});

const days = [
    'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'
];

const fetchSchedules = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/schedules');
        schedules.value = response.data;
    } catch (error) {
        console.error('Failed to fetch schedules', error);
    } finally {
        loading.value = false;
    }
};

const fetchMasterData = async () => {
    try {
        const [classesRes, subjectsRes, teachersRes] = await Promise.all([
            axios.get('/api/school-classes'),
            axios.get('/api/subjects'),
            axios.get('/api/teachers'),
        ]);
        classes.value = classesRes.data;
        subjects.value = subjectsRes.data;
        teachers.value = teachersRes.data;
        console.log('Master data loaded', { classes: classes.value, subjects: subjects.value, teachers: teachers.value });
    } catch (error) {
        console.error('Failed to fetch master data', error);
    }
};

onMounted(() => {
    fetchSchedules();
    fetchMasterData();
});

const openCreateDialog = () => {
    isEditing.value = false;
    currentId.value = null;
    form.value = {
        school_class_id: '',
        subject_id: '',
        user_id: '',
        day_of_week: 'monday',
        start_time: '',
        end_time: '',
    };
    isDialogOpen.value = true;
};

const openEditDialog = (schedule: Schedule) => {
    isEditing.value = true;
    currentId.value = schedule.id;
    form.value = {
        school_class_id: String(schedule.school_class_id),
        subject_id: String(schedule.subject_id),
        user_id: String(schedule.user_id),
        day_of_week: schedule.day_of_week,
        start_time: schedule.start_time.substring(0, 5), // HH:mm
        end_time: schedule.end_time.substring(0, 5), // HH:mm
    };
    isDialogOpen.value = true;
};

const handleSubmit = async () => {
    try {
        if (isEditing.value && currentId.value) {
            await axios.put(`/api/schedules/${currentId.value}`, form.value);
        } else {
            await axios.post('/api/schedules', form.value);
        }
        isDialogOpen.value = false;
        fetchSchedules();
    } catch (error) {
        console.error('Failed to save schedule', error);
    }
};

const deleteSchedule = async (id: number) => {
    if (!confirm('Are you sure you want to delete this schedule?')) return;
    try {
        await axios.delete(`/api/schedules/${id}`);
        fetchSchedules();
    } catch (error) {
        console.error('Failed to delete schedule', error);
    }
};

const getDayName = (day: string) => {
    return day.charAt(0).toUpperCase() + day.slice(1);
};

const selectedClassName = computed(() => {
    if (!Array.isArray(classes.value)) return 'Select Class...';
    const cls = classes.value.find(c => String(c.id) === form.value.school_class_id);
    return cls ? cls.name : 'Select Class...';
});

const selectedSubjectName = computed(() => {
    if (!Array.isArray(subjects.value)) return 'Select Subject...';
    const sub = subjects.value.find(s => String(s.id) === form.value.subject_id);
    return sub ? sub.name : 'Select Subject...';
});

const selectedTeacherName = computed(() => {
    if (!Array.isArray(teachers.value)) return 'Select Teacher...';
    const teacher = teachers.value.find(t => String(t.id) === form.value.user_id);
    return teacher ? teacher.name : 'Select Teacher...';
});

const selectedDayName = computed(() => {
    return getDayName(form.value.day_of_week);
});

</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Master Data', href: '/master' }, { title: 'Jadwal Pelajaran', href: '/master/schedules' }]">
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Data Jadwal Pelajaran</h1>
                    <p class="text-sm text-muted-foreground">Manage lesson schedules.</p>
                </div>
                <Button @click="openCreateDialog">
                    <Plus class="mr-2 h-4 w-4" />
                    Add Schedule
                </Button>
            </div>

            <div class="rounded-xl border bg-card shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="border-b bg-muted/50 text-left text-muted-foreground">
                                <th class="py-3 px-4">Day</th>
                                <th class="py-3 px-4">Time</th>
                                <th class="py-3 px-4">Class</th>
                                <th class="py-3 px-4">Subject</th>
                                <th class="py-3 px-4">Teacher</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="schedule in schedules" :key="schedule.id" class="border-b last:border-0 hover:bg-muted/50">
                                <td class="py-3 px-4">{{ getDayName(schedule.day_of_week) }}</td>
                                <td class="py-3 px-4">{{ schedule.start_time.substring(0, 5) }} - {{ schedule.end_time.substring(0, 5) }}</td>
                                <td class="py-3 px-4">{{ schedule.class?.name }}</td>
                                <td class="py-3 px-4">{{ schedule.subject?.name }}</td>
                                <td class="py-3 px-4">{{ schedule.teacher?.name }}</td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <button class="text-indigo-600 hover:text-indigo-900 mr-2" @click="openEditDialog(schedule)">
                                        <Pencil class="h-4 w-4 inline" />
                                    </button>
                                    <button class="text-red-600 hover:text-red-900" @click="deleteSchedule(schedule.id)">
                                        <Trash2 class="h-4 w-4 inline" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="schedules.length === 0">
                                <td colspan="6" class="text-center py-8 text-muted-foreground">
                                    No schedules found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <Dialog v-model:open="isDialogOpen">
                <DialogContent class="sm:max-w-[500px]">
                    <DialogHeader>
                        <DialogTitle class="text-xl">{{ isEditing ? 'Edit Schedule' : 'Add New Schedule' }}</DialogTitle>
                        <DialogDescription>
                            Configure the lesson schedule details below.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-6 py-4">
                         <div class="grid gap-2">
                            <Label>Class Room</Label>
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="outline" class="w-full justify-between font-normal" :class="!form.school_class_id && 'text-muted-foreground'">
                                        {{ selectedClassName }}
                                        <ChevronDown class="ml-2 h-4 w-4 opacity-50" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent class="w-[450px] max-h-[300px] overflow-y-auto" align="start">
                                    <DropdownMenuItem v-for="cls in classes" :key="cls.id" @click="form.school_class_id = String(cls.id)">
                                        {{ cls.name }}
                                        <Check v-if="form.school_class_id === String(cls.id)" class="ml-auto h-4 w-4" />
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>

                        <div class="grid gap-2">
                            <Label>Subject</Label>
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="outline" class="w-full justify-between font-normal" :class="!form.subject_id && 'text-muted-foreground'">
                                        {{ selectedSubjectName }}
                                        <ChevronDown class="ml-2 h-4 w-4 opacity-50" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent class="w-[450px] max-h-[300px] overflow-y-auto" align="start">
                                    <DropdownMenuItem v-for="sub in subjects" :key="sub.id" @click="form.subject_id = String(sub.id)">
                                        {{ sub.name }}
                                        <Check v-if="form.subject_id === String(sub.id)" class="ml-auto h-4 w-4" />
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>

                        <div class="grid gap-2">
                            <Label>Teacher</Label>
                             <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="outline" class="w-full justify-between font-normal" :class="!form.user_id && 'text-muted-foreground'">
                                        {{ selectedTeacherName }}
                                        <ChevronDown class="ml-2 h-4 w-4 opacity-50" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent class="w-[450px] max-h-[300px] overflow-y-auto" align="start">
                                    <DropdownMenuItem v-for="teacher in teachers" :key="teacher.id" @click="form.user_id = String(teacher.id)">
                                        {{ teacher.name }}
                                        <Check v-if="form.user_id === String(teacher.id)" class="ml-auto h-4 w-4" />
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>
                        
                        <div class="grid gap-2">
                            <Label>Day</Label>
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="outline" class="w-full justify-between font-normal">
                                        {{ selectedDayName }}
                                        <ChevronDown class="ml-2 h-4 w-4 opacity-50" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent class="w-[450px]" align="start">
                                    <DropdownMenuItem v-for="day in days" :key="day" @click="form.day_of_week = day">
                                        {{ getDayName(day) }}
                                        <Check v-if="form.day_of_week === day" class="ml-auto h-4 w-4" />
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label>Start Time</Label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <Clock class="h-4 w-4 text-muted-foreground" />
                                    </div>
                                    <input 
                                        type="time" 
                                        v-model="form.start_time"
                                        class="flex h-10 w-full rounded-md border border-input bg-background pl-10 pr-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    />
                                </div>
                            </div>
                            <div class="grid gap-2">
                                <Label>End Time</Label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <Clock class="h-4 w-4 text-muted-foreground" />
                                    </div>
                                    <input 
                                        type="time" 
                                        v-model="form.end_time"
                                        class="flex h-10 w-full rounded-md border border-input bg-background pl-10 pr-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button type="submit" @click="handleSubmit">{{ isEditing ? 'Update Schedule' : 'Save Schedule' }}</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
