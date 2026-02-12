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
    DialogTrigger,
} from '@/components/ui/dialog';
import InputError from '@/components/InputError.vue';
import axios from 'axios';

interface SchoolClass {
    id: number;
    name: string;
    slug: string;
    created_at: string;
    updated_at: string;
}

const classes = ref<SchoolClass[]>([]);
const editingClass = ref<SchoolClass | null>(null);
const isDialogOpen = ref(false);
const nameInput = ref<HTMLInputElement | null>(null);

const form = ref({
    name: '',
    errors: {} as Record<string, string>,
    processing: false,
});

const fetchClasses = async () => {
    try {
        const response = await axios.get('/api/school-classes');
        classes.value = response.data;
    } catch (error) {
        console.error('Failed to fetch classes', error);
    }
};

onMounted(() => {
    fetchClasses();
});

const openCreateDialog = async () => {
    editingClass.value = null;
    form.value.name = '';
    form.value.errors = {};
    isDialogOpen.value = true;
    
    await nextTick();
    nameInput.value?.focus();
};

const openEditDialog = async (schoolClass: SchoolClass) => {
    editingClass.value = schoolClass;
    form.value.name = schoolClass.name;
    form.value.errors = {};
    isDialogOpen.value = true;

    await nextTick();
    nameInput.value?.focus();
};

const closeDialog = () => {
    isDialogOpen.value = false;
    form.value.name = '';
    form.value.errors = {};
    editingClass.value = null;
};

const submit = async () => {
    form.value.processing = true;
    form.value.errors = {};

    try {
        if (editingClass.value) {
            await axios.put(`/api/school-classes/${editingClass.value.id}`, {
                name: form.value.name,
            });
        } else {
            await axios.post('/api/school-classes', {
                name: form.value.name,
            });
        }
        await fetchClasses();
        closeDialog();
    } catch (error: any) {
        if (error.response && error.response.data.errors) {
            form.value.errors = error.response.data.errors;
        }
    } finally {
        form.value.processing = false;
    }
};

const deleteClass = async (id: number) => {
    if (confirm('Are you sure you want to delete this class?')) {
        try {
            await axios.delete(`/api/school-classes/${id}`);
            await fetchClasses();
        } catch (error) {
            console.error('Failed to delete class', error);
        }
    }
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Master Data', href: '/master' },
    { title: 'Data Kelas', href: '/master/classes' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Data Kelas</h1>
                    <p class="text-sm text-muted-foreground">Manage school classes here.</p>
                </div>

                <button
                    @click="openCreateDialog"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    + Add Class
                </button>
            </div>

            <div class="rounded-xl border bg-card p-6 shadow-sm">
                <div v-if="classes.length" class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="border-b text-left text-muted-foreground">
                                <th class="py-3 px-4">Name</th>
                                <th class="py-3 px-4">Slug</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="schoolClass in classes"
                                :key="schoolClass.id"
                                class="border-b last:border-0 hover:bg-muted/50"
                            >
                                <td class="py-4 px-4 font-medium">{{ schoolClass.name }}</td>
                                <td class="py-4 px-4 text-muted-foreground">{{ schoolClass.slug }}</td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            @click="openEditDialog(schoolClass)"
                                            class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="deleteClass(schoolClass.id)"
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
                    <div class="text-4xl">📚</div>
                    <h3 class="text-lg font-medium">No classes yet</h3>
                    <p class="max-w-sm text-sm text-muted-foreground">
                        You haven’t added any classes yet. Start by adding your first class.
                    </p>
                    <button
                        @click="openCreateDialog"
                        class="mt-4 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                    >
                        Add first class
                    </button>
                </div>
            </div>
        </div>

        <Dialog :open="isDialogOpen" @update:open="isDialogOpen = $event">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ editingClass ? 'Edit Class' : 'Add New Class' }}</DialogTitle>
                    <DialogDescription>
                        {{ editingClass ? 'Update the class details below.' : 'Enter the details for the new class.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid gap-2">
                        <label for="name" class="text-sm font-medium">Class Name</label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-full"
                            placeholder="e.g., X RPL 1"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
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
                            {{ editingClass ? 'Save Changes' : 'Create Class' }}
                        </button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
