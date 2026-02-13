<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import axios from 'axios';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Password settings',
        href: '/settings/password',
    },
];

const form = ref({
    current_password: '',
    password: '',
    password_confirmation: '',
    processing: false,
    errors: {} as Record<string, string>,
});

const recentlySuccessful = ref(false);

const updatePassword = async () => {
    form.value.processing = true;
    form.value.errors = {};
    recentlySuccessful.value = false;

    try {
        await axios.put('/api/password', {
            current_password: form.value.current_password,
            password: form.value.password,
            password_confirmation: form.value.password_confirmation,
        });
        recentlySuccessful.value = true;
        form.value.current_password = '';
        form.value.password = '';
        form.value.password_confirmation = '';
        setTimeout(() => recentlySuccessful.value = false, 2000);
    } catch (error: any) {
        if (error.response && error.response.data.errors) {
            form.value.errors = error.response.data.errors;
        }
    } finally {
        form.value.processing = false;
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Password settings" />
        <h1 class="sr-only">Password Settings</h1>

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    title="Update password"
                    description="Ensure your account is using a long, random password to stay secure"
                />

                <form @submit.prevent="updatePassword" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="current_password">Current Password</Label>
                        <Input
                            id="current_password"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.current_password"
                            required
                            autocomplete="current-password"
                            placeholder="Current password"
                        />
                        <InputError class="mt-2" :message="form.errors.current_password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">New Password</Label>
                        <Input
                            id="password"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            placeholder="New password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation">Confirm Password</Label>
                        <Input
                            id="password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Confirm password"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save password</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="recentlySuccessful"
                                class="text-sm text-neutral-600"
                            >
                                Saved.
                            </p>
                        </Transition>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
