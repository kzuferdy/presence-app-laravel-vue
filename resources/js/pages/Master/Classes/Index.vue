<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
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


//test


interface SchoolClass {
    id: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    classes: SchoolClass[];
}>();

const isDialogOpen = ref(false);
const editingClass = ref<SchoolClass | null>(null);

const form = useForm({
    name: '',
});

const openCreate = () => {
    editingClass.value = null;
    form.reset();
    form.clearErrors();
    isDialogOpen.value = true;
};

const openEdit = (schoolClass: SchoolClass) => {
    editingClass.value = schoolClass;
    form.name = schoolClass.name;
    form.clearErrors();
    isDialogOpen.value = true;
};

const closeDialog = () => {
    isDialogOpen.value = false;
    form.reset();
    editingClass.value = null;
};

const submit = () => {
    if (editingClass.value) {
        form.put(`/master/classes/${editingClass.value.id}`, {
            onSuccess: () => closeDialog(),
        });
    } else {
        form.post('/master/classes', {
            onSuccess: () => closeDialog(),
        });
    }
};

const deleteClass = (id: number) => {
    if (confirm('Are you sure you want to delete this class?')) {
        router.delete(`/master/classes/${id}`);
    }
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Master Data', href: '#' },
    { title: 'Data Kelas', href: '/master/classes' },
];
</script>

<template>
    <Head title="Data Kelas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Data Kelas</h1>
                    <p class="text-sm text-muted-foreground">Manage school classes here.</p>
                </div>

                <button
                    @click="openCreate"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    + Add Class
                </button>
            </div>

            <div class="rounded-xl border bg-card p-6 shadow-sm">
                <div v-if="props.classes.length" class="overflow-x-auto">
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
                                            @click="openEdit(schoolClass)"
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
                        @click="openCreate"
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
                        <InputError :message="form.errors.name" />
                    </div>

                    <DialogFooter>
                        <button
                            type="button"
                            @click="closeDialog"
                            class="inline-flex items-center justify-center rounded-md border border-input bg-transparent px-4 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-700 disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            Save
                        </button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
