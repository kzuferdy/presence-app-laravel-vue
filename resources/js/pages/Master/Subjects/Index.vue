<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { nextTick, ref, onMounted } from 'vue';
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
import { computed } from 'vue';

interface Subject {
    id: number;
    name: string;
    code: string | null;
    created_at: string;
    updated_at: string;
}

const subjects = ref<Subject[]>([]);
const editingSubject = ref<Subject | null>(null);
const isDialogOpen = ref(false);
const nameInput = ref<HTMLInputElement | null>(null);

const form = ref({
    name: '',
    code: '',
    errors: {} as Record<string, string>,
    processing: false,
});

const fetchSubjects = async () => {
    try {
        const response = await axios.get('/api/subjects');
        subjects.value = response.data;
    } catch (error) {
        console.error('Failed to fetch subjects', error);
    }
};

onMounted(() => {
    fetchSubjects();
});

const openCreateDialog = async () => {
    editingSubject.value = null;
    form.value.name = '';
    form.value.code = '';
    form.value.errors = {};
    isDialogOpen.value = true;
    
    await nextTick();
    nameInput.value?.focus();
};

const openEditDialog = async (subject: Subject) => {
    editingSubject.value = subject;
    form.value.name = subject.name;
    form.value.code = subject.code || '';
    form.value.errors = {};
    isDialogOpen.value = true;

    await nextTick();
    nameInput.value?.focus();
};

const closeDialog = () => {
    isDialogOpen.value = false;
    form.value.name = '';
    form.value.code = '';
    form.value.errors = {};
    editingSubject.value = null;
};

const submit = async () => {
    form.value.processing = true;
    form.value.errors = {};

    try {
        if (editingSubject.value) {
            await axios.put(`/api/subjects/${editingSubject.value.id}`, {
                name: form.value.name,
                code: form.value.code,
            });
        } else {
            await axios.post('/api/subjects', {
                name: form.value.name,
                code: form.value.code,
            });
        }
        await fetchSubjects();
        closeDialog();
    } catch (error: any) {
        if (error.response && error.response.data.errors) {
            form.value.errors = error.response.data.errors;
        }
    } finally {
        form.value.processing = false;
    }
};

const deleteSubject = async (id: number) => {
    if (confirm('Are you sure you want to delete this subject?')) {
        try {
            await axios.delete(`/api/subjects/${id}`);
            await fetchSubjects();
        } catch (error) {
            console.error('Failed to delete subject', error);
        }
    }
};

const { user } = useAuth();
const isPrincipal = computed(() => user.value?.role === 'principal');

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Master Data', href: '/master' },
    { title: 'Data Pelajaran', href: '/master/subjects' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Data Pelajaran</h1>
                    <p class="text-sm text-muted-foreground">Manage subjects (Mata Pelajaran) here.</p>
                </div>

                <button
                    v-if="!isPrincipal"
                    @click="openCreateDialog"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    + Add Subject
                </button>
            </div>

            <div class="rounded-xl border bg-card p-6 shadow-sm">
                <div v-if="subjects.length" class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="border-b text-left text-muted-foreground">
                                <th class="py-3 px-4">Name</th>
                                <th class="py-3 px-4">Code</th>
                                <th v-if="!isPrincipal" class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="subject in subjects"
                                :key="subject.id"
                                class="border-b last:border-0 hover:bg-muted/50"
                            >
                                <td class="py-4 px-4 font-medium">{{ subject.name }}</td>
                                <td class="py-4 px-4 text-muted-foreground">{{ subject.code || '-' }}</td>
                                <td v-if="!isPrincipal" class="py-4 px-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            @click="openEditDialog(subject)"
                                            class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="deleteSubject(subject.id)"
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
                    <div class="text-4xl">📘</div>
                    <h3 class="text-lg font-medium">No subjects yet</h3>
                    <p class="max-w-sm text-sm text-muted-foreground">
                        You haven’t added any subjects yet. Start by adding your first subject.
                    </p>
                    <button
                        @click="openCreateDialog"
                        class="mt-4 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                    >
                        Add Subject
                    </button>
                </div>
            </div>
        </div>

        <Dialog :open="isDialogOpen" @update:open="isDialogOpen = $event">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ editingSubject ? 'Edit Subject' : 'Add New Subject' }}</DialogTitle>
                    <DialogDescription>
                        {{ editingSubject ? 'Update the subject details below.' : 'Enter the details for the new subject.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid gap-2">
                        <label for="name" class="text-sm font-medium">Subject Name</label>
                        <input
                            id="name"
                            ref="nameInput"
                            v-model="form.name"
                            type="text"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-full"
                            placeholder="e.g., Matematika"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <label for="code" class="text-sm font-medium">Subject Code (Optional)</label>
                        <input
                            id="code"
                            v-model="form.code"
                            type="text"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-full"
                            placeholder="e.g., MTK"
                        />
                        <InputError :message="form.errors.code" class="mt-2" />
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
                            {{ editingSubject ? 'Save Changes' : 'Create Subject' }}
                        </button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
