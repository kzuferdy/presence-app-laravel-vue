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

interface AcademicYear {
    id: number;
    name: string;
    start_date: string;
    end_date: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

const academicYears = ref<AcademicYear[]>([]);
const editingYear = ref<AcademicYear | null>(null);
const isDialogOpen = ref(false);
const nameInput = ref<HTMLInputElement | null>(null);

const form = ref({
    name: '',
    start_date: '',
    end_date: '',
    is_active: false,
    errors: {} as Record<string, string>,
    processing: false,
});

const fetchData = async () => {
    try {
        const response = await axios.get('/api/academic-years');
        academicYears.value = response.data;
    } catch (error) {
        console.error('Failed to fetch academic years', error);
    }
};

onMounted(() => {
    fetchData();
});

const openCreateDialog = async () => {
    editingYear.value = null;
    form.value.name = '';
    form.value.start_date = '';
    form.value.end_date = '';
    form.value.is_active = false;
    form.value.errors = {};
    isDialogOpen.value = true;
    
    await nextTick();
    nameInput.value?.focus();
};

const openEditDialog = async (year: AcademicYear) => {
    editingYear.value = year;
    form.value.name = year.name;
    form.value.start_date = year.start_date;
    form.value.end_date = year.end_date;
    form.value.is_active = Boolean(year.is_active);
    form.value.errors = {};
    isDialogOpen.value = true;

    await nextTick();
    nameInput.value?.focus();
};

const closeDialog = () => {
    isDialogOpen.value = false;
    form.value.name = '';
    form.value.start_date = '';
    form.value.end_date = '';
    form.value.is_active = false;
    form.value.errors = {};
    editingYear.value = null;
};

const submit = async () => {
    form.value.processing = true;
    form.value.errors = {};

    try {
        if (editingYear.value) {
            await axios.put(`/api/academic-years/${editingYear.value.id}`, {
                name: form.value.name,
                start_date: form.value.start_date,
                end_date: form.value.end_date,
                is_active: form.value.is_active,
            });
        } else {
            await axios.post('/api/academic-years', {
                name: form.value.name,
                start_date: form.value.start_date,
                end_date: form.value.end_date,
                is_active: form.value.is_active,
            });
        }
        await fetchData();
        closeDialog();
    } catch (error: any) {
        if (error.response && error.response.data.errors) {
            form.value.errors = error.response.data.errors;
        }
    } finally {
        form.value.processing = false;
    }
};

const deleteYear = async (id: number) => {
    if (confirm('Are you sure you want to delete this academic year?')) {
        try {
            await axios.delete(`/api/academic-years/${id}`);
            await fetchData();
        } catch (error) {
            console.error('Failed to delete academic year', error);
        }
    }
};

import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useAuth } from '@/composables/useAuth';

const { user } = useAuth();
const isPrincipal = computed(() => user.value?.role === 'principal');

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Master Data', href: '/master' },
    { title: 'Data Tahun Ajaran', href: '/master/academic-years' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Data Tahun Ajaran</h1>
                    <p class="text-sm text-muted-foreground">Manage academic years here.</p>
                </div>

                <button
                    v-if="!isPrincipal"
                    @click="openCreateDialog"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    + Add Academic Year
                </button>
            </div>

            <div class="rounded-xl border bg-card p-6 shadow-sm">
                <div v-if="academicYears.length" class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="border-b text-left text-muted-foreground">
                                <th class="py-3 px-4">Name</th>
                                <th class="py-3 px-4">Start Date</th>
                                <th class="py-3 px-4">End Date</th>
                                <th class="py-3 px-4">Active</th>
                                <th v-if="!isPrincipal" class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="year in academicYears"
                                :key="year.id"
                                class="border-b last:border-0 hover:bg-muted/50"
                            >
                                <td class="py-4 px-4 font-medium">{{ year.name }}</td>
                                <td class="py-4 px-4 font-medium">{{ year.start_date }}</td>
                                <td class="py-4 px-4 font-medium">{{ year.end_date }}</td>
                                <td class="py-4 px-4 font-medium">
                                    <span v-if="year.is_active" class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                        Active
                                    </span>
                                    <span v-else class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
                                        Inactive
                                    </span>
                                </td>
                                <td v-if="!isPrincipal" class="py-4 px-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            @click="openEditDialog(year)"
                                            class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="deleteYear(year.id)"
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
                    <div class="text-4xl">📅</div>
                    <h3 class="text-lg font-medium">No academic years yet</h3>
                    <p class="max-w-sm text-sm text-muted-foreground">
                        You haven’t added any academic years yet. Start by adding your first one.
                    </p>
                    <button
                        @click="openCreateDialog"
                        class="mt-4 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                    >
                        Add first academic year
                    </button>
                </div>
            </div>
        </div>

        <Dialog :open="isDialogOpen" @update:open="isDialogOpen = $event">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ editingYear ? 'Edit Academic Year' : 'Add New Academic Year' }}</DialogTitle>
                    <DialogDescription>
                        {{ editingYear ? 'Update details below.' : 'Enter details for the new academic year.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid gap-2">
                        <label for="name" class="text-sm font-medium">Academic Year Name</label>
                        <input
                            id="name"
                            v-model="form.name"
                            ref="nameInput"
                            type="text"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-full"
                            placeholder="e.g., 2025/2026"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <label for="start_date" class="text-sm font-medium">Start Date</label>
                        <input
                            id="start_date"
                            v-model="form.start_date"
                            type="date"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-full"
                            required
                        />
                        <InputError :message="form.errors.start_date" class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <label for="end_date" class="text-sm font-medium">End Date</label>
                        <input
                            id="end_date"
                            v-model="form.end_date"
                            type="date"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring w-full"
                            required
                        />
                        <InputError :message="form.errors.end_date" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-2">
                        <input
                            id="is_active"
                            v-model="form.is_active"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                        />
                        <label for="is_active" class="text-sm font-medium">Set as Active Year</label>
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
                            {{ editingYear ? 'Save Changes' : 'Create Academic Year' }}
                        </button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
