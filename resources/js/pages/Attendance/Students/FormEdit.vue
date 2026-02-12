<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';

const props = defineProps<{
    student: {
        id: number;
        name: string;
        nis: string;
        class: string;
        address: string;
        school_class_id: number | null;
    };
    classes: { id: number; name: string }[];
}>();

const form = useForm({
    name: props.student.name,
    nis: props.student.nis,
    class: props.student.class,
    school_class_id: props.student.school_class_id,
    photo: null as File | null,
    address: props.student.address,
    _method: 'put',
});

const submit = () => {
    form.post(`/attendance/students/${props.student.id}`);
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Attendance', href: '/attendance' },
    { title: 'Data Siswa', href: '/attendance/students' },
    { title: 'Edit Student', href: `/attendance/students/${props.student.id}/edit` },
];
</script>

<template>
    <Head title="Edit Student" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl space-y-8">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Edit Student</h1>
                <p class="text-sm text-muted-foreground">Update student details.</p>
            </div>

            <div class="rounded-xl border bg-card p-6 shadow-sm">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <label for="name" class="text-sm font-medium">Name</label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="Enter student name"
                            required
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <label for="nis" class="text-sm font-medium">NIS</label>
                        <input
                            id="nis"
                            v-model="form.nis"
                            type="text"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="Enter NIS"
                            required
                        />
                        <InputError :message="form.errors.nis" />
                    </div>

                    <div class="grid gap-2">
                        <label for="school_class_id" class="text-sm font-medium">Class</label>
                        <select
                            id="school_class_id"
                            v-model="form.school_class_id"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            required
                            @change="
                                () => {
                                    const selectedClass = props.classes.find((c) => c.id === form.school_class_id);
                                    if (selectedClass) {
                                        form.class = selectedClass.name;
                                    }
                                }
                            "
                        >
                            <option :value="null" disabled>Select a class</option>
                            <option v-for="schoolClass in classes" :key="schoolClass.id" :value="schoolClass.id">
                                {{ schoolClass.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.school_class_id" />
                    </div>

                    <div class="grid gap-2">
                        <label for="address" class="text-sm font-medium">Address</label>
                        <textarea
                            id="address"
                            v-model="form.address"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="Enter address"
                            rows="3"
                        ></textarea>
                        <InputError :message="form.errors.address" />
                    </div>

                    <div class="grid gap-2">
                        <label for="photo" class="text-sm font-medium">Photo (Leave blank to keep current)</label>
                        <input
                            id="photo"
                            type="file"
                            @input="form.photo = ($event.target as HTMLInputElement).files?.[0] || null"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            accept="image/*"
                        />
                        <InputError :message="form.errors.photo" />
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <Link
                            href="/attendance/students"
                            class="inline-flex items-center justify-center rounded-md border border-input bg-transparent px-4 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90 disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            Update Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
