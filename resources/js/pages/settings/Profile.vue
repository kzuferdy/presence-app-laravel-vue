<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import { useAuth } from '@/composables/useAuth';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';

interface Props {
    mustVerifyEmail?: boolean;
    status?: string;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const { user, fetchUser } = useAuth();
const processing = ref(false);
const recentlySuccessful = ref(false);
const errors = ref<Record<string, string>>({});

const form = ref({
    name: user.value?.name || '',
    email: user.value?.email || '',
});

const updateProfile = async () => {
    processing.value = true;
    errors.value = {};
    recentlySuccessful.value = false;

    try {
        const response = await axios.patch('/api/profile', form.value);
        // Refresh user data if name/email changed
        await fetchUser();
        recentlySuccessful.value = true;
        setTimeout(() => recentlySuccessful.value = false, 2000);
    } catch (error: any) {
        if (error.response && error.response.data.errors) {
            errors.value = error.response.data.errors;
        } else {
            console.error(error);
        }
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <h1 class="sr-only">Profile Settings</h1>

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    title="Profile information"
                    description="Update your name and email address"
                />

                <form @submit.prevent="updateProfile" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            class="mt-1 block w-full"
                            name="name"
                            v-model="form.name"
                            required
                            autocomplete="name"
                            placeholder="Full name"
                        />
                        <InputError class="mt-2" :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            name="email"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="errors.email" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            :disabled="processing"
                            data-test="update-profile-button"
                            >Save</Button
                        >

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

            <!-- DeleteUser component may need refactor too, but for now ignoring -->
            <!-- <DeleteUser /> -->
        </SettingsLayout>
    </AppLayout>
</template>
