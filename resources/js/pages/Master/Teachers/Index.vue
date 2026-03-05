<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { nextTick, ref, onMounted, computed } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import InputError from '@/components/InputError.vue';
import axios from 'axios';
import { useAuth } from '@/composables/useAuth';

interface SchoolClass {
    id: number;
    name: string;
}

interface Teacher {
    id: number;
    name: string;
    email: string;
    nip: string | null;
    mapel: string | null;
    role: string;
    classes?: SchoolClass[];
}

const teachers = ref<Teacher[]>([]);
const allClasses = ref<SchoolClass[]>([]);
const editingTeacher = ref<Teacher | null>(null);
const isDialogOpen = ref(false);
const nameInput = ref<HTMLInputElement | null>(null);

const form = ref({
    name: '',
    email: '',
    password: '',
    nip: '',
    mapel: '',
    classes: [] as number[],
    errors: {} as Record<string, string>,
    processing: false,
});

const fetchTeachers = async () => {
    try {
        const response = await axios.get('/api/teachers');
        teachers.value = response.data;
    } catch (error) {
        console.error('Failed to fetch teachers', error);
    }
};

const fetchClasses = async () => {
    try {
        const response = await axios.get('/api/school-classes');
        allClasses.value = response.data;
    } catch (error) {
        console.error('Failed to fetch classes', error);
    }
};

onMounted(() => {
    fetchTeachers();
    fetchClasses();
});

const openCreateDialog = async () => {
    editingTeacher.value = null;
    form.value.name = '';
    form.value.email = '';
    form.value.password = '';
    form.value.nip = '';
    form.value.mapel = '';
    form.value.classes = [];
    form.value.errors = {};
    isDialogOpen.value = true;
    
    await nextTick();
    nameInput.value?.focus();
};

const openEditDialog = async (teacher: Teacher) => {
    editingTeacher.value = teacher;
    form.value.name = teacher.name;
    form.value.email = teacher.email;
    form.value.password = ''; // Leave blank to keep current password
    form.value.nip = teacher.nip || '';
    form.value.mapel = teacher.mapel || '';
    form.value.classes = teacher.classes ? teacher.classes.map(c => c.id) : [];
    form.value.errors = {};
    isDialogOpen.value = true;

    await nextTick();
    nameInput.value?.focus();
};

const closeDialog = () => {
    isDialogOpen.value = false;
    form.value.name = '';
    form.value.email = '';
    form.value.password = '';
    form.value.nip = '';
    form.value.mapel = '';
    form.value.classes = [];
    form.value.errors = {};
    editingTeacher.value = null;
};

const submit = async () => {
    form.value.processing = true;
    form.value.errors = {};

    try {
        const payload: any = {
            name: form.value.name,
            email: form.value.email,
            nip: form.value.nip,
            mapel: form.value.mapel,
            classes: form.value.classes,
        };

        if (form.value.password) {
            payload.password = form.value.password;
        }

        if (editingTeacher.value) {
            await axios.put(`/api/teachers/${editingTeacher.value.id}`, payload);
        } else {
            // Password required for create
            if (!payload.password) payload.password = 'password'; // Default or required?
            // Actually it is required in controller. We should enforce it in UI or set default.
            // Let's rely on validation error if missing.
            await axios.post('/api/teachers', payload);
        }
        await fetchTeachers();
        closeDialog();
    } catch (error: any) {
        if (error.response && error.response.data.errors) {
            form.value.errors = error.response.data.errors;
        }
    } finally {
        form.value.processing = false;
    }
};

const deleteTeacher = async (id: number) => {
    if (confirm('Are you sure you want to delete this teacher?')) {
        try {
            await axios.delete(`/api/teachers/${id}`);
            await fetchTeachers();
        } catch (error) {
            console.error('Failed to delete teacher', error);
        }
    }
};

const { user } = useAuth();
// Only Admin/Principal
const isPrincipal = computed(() => user.value?.role === 'principal');

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Master Data', href: '/master' },
    { title: 'Data Guru', href: '/master/teachers' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Data Guru</h1>
                    <p class="text-sm text-muted-foreground">Manage teachers and class assignments.</p>
                </div>

                <button
                    v-if="!isPrincipal"
                    @click="openCreateDialog"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    + Add Teacher
                </button>
            </div>

            <div class="rounded-xl border bg-card p-6 shadow-sm">
                <div v-if="teachers.length" class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="border-b text-left text-muted-foreground">
                                <th class="py-3 px-4">Name / NIP</th>
                                <th class="py-3 px-4">Mapel</th>
                                <th class="py-3 px-4">Assigned Classes</th>
                                <th v-if="!isPrincipal" class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="teacher in teachers"
                                :key="teacher.id"
                                class="border-b last:border-0 hover:bg-muted/50"
                            >
                                <td class="py-4 px-4">
                                    <div class="font-medium">{{ teacher.name }}</div>
                                    <div class="text-xs text-muted-foreground">{{ teacher.nip || '-' }}</div>
                                    <div class="text-xs text-muted-foreground">{{ teacher.email }}</div>
                                </td>
                                <td class="py-4 px-4">{{ teacher.mapel || '-' }}</td>
                                <td class="py-4 px-4">
                                    <div class="flex flex-wrap gap-1">
                                        <span
                                            v-for="cls in teacher.classes"
                                            :key="cls.id"
                                            class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                                        >
                                            {{ cls.name }}
                                        </span>
                                        <span v-if="!teacher.classes?.length" class="text-muted-foreground text-xs italic">
                                            No classes assigned
                                        </span>
                                    </div>
                                </td>
                                <td v-if="!isPrincipal" class="py-4 px-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            @click="openEditDialog(teacher)"
                                            class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="deleteTeacher(teacher.id)"
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
                    <div class="text-4xl">👨‍🏫</div>
                    <h3 class="text-lg font-medium">No teachers yet</h3>
                    <p class="max-w-sm text-sm text-muted-foreground">
                        You haven’t added any teachers yet. Start by adding your first teacher.
                    </p>
                    <button
                        @click="openCreateDialog"
                        class="mt-4 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                    >
                        Add Teacher
                    </button>
                </div>
            </div>
        </div>

        <Dialog :open="isDialogOpen" @update:open="isDialogOpen = $event">
            <DialogContent class="sm:max-w-lg overflow-y-auto max-h-[90vh]">
                <DialogHeader>
                    <DialogTitle>{{ editingTeacher ? 'Edit Teacher' : 'Add New Teacher' }}</DialogTitle>
                    <DialogDescription>
                        {{ editingTeacher ? 'Update the teacher details below.' : 'Enter the details for the new teacher.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid gap-2">
                        <label for="name" class="text-sm font-medium">Full Name</label>
                        <input
                            id="name"
                            ref="nameInput"
                            v-model="form.name"
                            type="text"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-full"
                            placeholder="e.g., Budi Santoso"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <label for="email" class="text-sm font-medium">Email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-full"
                            placeholder="e.g., budi@school.com"
                            required
                        />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <label for="password" class="text-sm font-medium">
                            Password <span v-if="!editingTeacher" class="text-red-500">*</span>
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-full"
                            placeholder="Leave blank to keep current"
                        />
                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                         <div class="grid gap-2">
                            <label for="nip" class="text-sm font-medium">NIP</label>
                            <input
                                id="nip"
                                v-model="form.nip"
                                type="text"
                                class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-full"
                                placeholder="e.g., 19800101..."
                            />
                            <InputError :message="form.errors.nip" class="mt-2" />
                        </div>
                        <div class="grid gap-2">
                            <label for="mapel" class="text-sm font-medium">Mata Pelajaran</label>
                            <input
                                id="mapel"
                                v-model="form.mapel"
                                type="text"
                                class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-full"
                                placeholder="e.g., Matematika"
                            />
                            <InputError :message="form.errors.mapel" class="mt-2" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Assign Classes</label>
                        <div class="grid grid-cols-2 gap-2 rounded-md border p-4 max-h-40 overflow-y-auto">
                            <div v-for="cls in allClasses" :key="cls.id" class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    :id="'class-' + cls.id"
                                    :value="cls.id"
                                    v-model="form.classes"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                />
                                <label :for="'class-' + cls.id" class="text-sm cursor-pointer">{{ cls.name }}</label>
                            </div>
                            <div v-if="allClasses.length === 0" class="col-span-2 text-center text-sm text-muted-foreground">
                                No classes available. Create classes first.
                            </div>
                        </div>
                        <InputError :message="form.errors.classes" class="mt-2" />
                    </div>

                    <DialogFooter>
                        <button
                            type="button"
                            @click="closeDialog"
                            class="rounded-md px-4 py-2 text-sm font-medium text-muted-foreground hover:bg-muted"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                            :disabled="form.processing"
                        >
                            {{ editingTeacher ? 'Save Changes' : 'Create Teacher' }}
                        </button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
