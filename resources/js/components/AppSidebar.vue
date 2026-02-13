<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';

import { type NavItem } from '@/types';
import { RouterLink } from 'vue-router';
import { BookOpen, ClipboardCheck, Folder, LayoutGrid, LogOut } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { useAuth } from '@/composables/useAuth';

const { logout } = useAuth();

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Attendance',
        href: '/attendance',
        icon: ClipboardCheck,
        items: [
            {
                title: 'Data Siswa',
                href: '/attendance/students',
            },
            {
                title: 'Presence',
                href: '/attendance/presence',
            },
            {
                title: 'Rekap Absen',
                href: '/attendance/recap',
            },
        ],
    },
    {
        title: 'Master Data',
        href: '/master',
        icon: Folder,
        items: [
            {
                title: 'Data Kelas',
                href: '/master/classes',
            },
        ],
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <RouterLink to="/dashboard">
                            <AppLogo />
                        </RouterLink>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
