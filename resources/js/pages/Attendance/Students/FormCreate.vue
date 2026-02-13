<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import InputError from '@/components/InputError.vue';
import axios from 'axios';

interface SchoolClass {
    id: number;
    name: string;
}

const classes = ref([] as SchoolClass[]);
const router = useRouter();

const form = ref({
    name: '',
    nis: '',
    school_class_id: '',
    address: '',
    photo: null as File | null,
    errors: {} as Record<string, string>,
    processing: false,
});

onMounted(async () => {
    try {
        const response = await axios.get('/api/school-classes');
        classes.value = response.data;
    } catch (error) {
        console.error('Failed to fetch classes', error);
    }
});

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.value.photo = target.files[0];
    }
};

const submit = async () => {
    form.value.processing = true;
    form.value.errors = {};

    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('nis', form.value.nis);
    formData.append('class', 'placeholder'); // Backend requires 'class' but we use school_class_id
    if (form.value.school_class_id) {
        formData.append('school_class_id', form.value.school_class_id);
    }
    if (form.value.address) {
        formData.append('address', form.value.address);
    }
    if (form.value.photo) {
        formData.append('photo', form.value.photo);
    }

    try {
        await axios.post('/api/students', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        router.push('/attendance/students');
    } catch (error: any) {
        if (error.response && error.response.data.errors) {
            form.value.errors = error.response.data.errors;
        }
    } finally {
        form.value.processing = false;
    }
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Attendance', href: '/attendance' },
    { title: 'Data Siswa', href: '/attendance/students' },
    { title: 'Tambah Siswa', href: '/attendance/students/create' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl space-y-8">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Add New Student</h1>
                <p class="text-sm text-muted-foreground">Fill in the details to add a new student.</p>
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
                        <label for="nis">NIS</label>
                        <input
                            id="nis"
                            v-model="form.nis"
                            type="text"
                            class="rounded-md border p-2"
                            required
                        />
                        <InputError :message="form.errors.nis" />
                    </div>

                    <div class="grid gap-2">
                        <label for="school_class_id">Class</label>
                        <select 
                            v-model="form.school_class_id"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option value="" disabled>Select Class</option>
                            <option
                                v-for="item in classes"
                                :key="item.id"
                                :value="String(item.id)"
                            >
                                {{ item.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.school_class_id" />
                    </div>

                    <div class="grid gap-2">
                        <label for="address">Address</label>
                        <textarea
                            id="address"
                            v-model="form.address"
                            class="rounded-md border p-2"
                        ></textarea>
                        <InputError :message="form.errors.address" />
                    </div>

                    <div class="grid gap-2">
                        <label for="photo">Photo</label>
                        <input
                            id="photo"
                            type="file"
                            @change="handleFileChange"
                            class="rounded-md border p-2"
                        />
                        <InputError :message="form.errors.photo" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <RouterLink
                            to="/attendance/students"
                            class="rounded-md px-4 py-2 text-sm font-medium hover:bg-muted"
                        >
                            Cancel
                        </RouterLink>
                        <button
                            type="submit"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700"
                            :disabled="form.processing"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
