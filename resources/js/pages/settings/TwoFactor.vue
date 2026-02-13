<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

// Simplified for now - fully implementing 2FA requires backend support which might not be ready
// Just rendering the UI shell to prevent crashes for now.

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Two-Factor Authentication',
        href: '/settings/two-factor',
    },
];

const twoFactorEnabled = false; // Placeholder
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Two-Factor Authentication" />

        <h1 class="sr-only">Two-Factor Authentication Settings</h1>

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    title="Two-Factor Authentication"
                    description="Manage your two-factor authentication settings"
                />

                <div class="flex flex-col items-start justify-start space-y-4">
                    <Badge variant="destructive">Disabled</Badge>
                    <p class="text-muted-foreground">
                        Two-factor authentication is currently not available in this demo.
                    </p>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
