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

// import { usePage } from '@inertiajs/vue3';
import { computed, markRaw } from 'vue';

const { user, logout } = useAuth();
// const page = usePage(); // usePage caused issues with undefined auth prop
const userRole = computed(() => user.value?.role);

// Use markRaw to prevent Vue from making icons reactive
const allNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: markRaw(LayoutGrid),
        roles: ['admin', 'teacher', 'principal'],
    },
    {
        title: 'Attendance',
        href: '/attendance',
        icon: markRaw(ClipboardCheck),
        roles: ['admin', 'teacher', 'principal'],
        items: [
            {
                title: 'Data Siswa',
                href: '/attendance/students',
                roles: ['admin', 'principal', 'teacher'],
            },
            {
                title: 'Presence',
                href: '/attendance/presence/create',
                roles: ['admin', 'teacher'],
            },
            {
                title: 'Rekap Absen',
                href: '/attendance/recap',
                roles: ['admin', 'principal', 'teacher'],
            },
        ],
    },
    {
        title: 'Master Data',
        href: '/master',
        icon: markRaw(Folder),
        roles: ['admin', 'principal'],
        items: [
            {
                title: 'Data Kelas',
                href: '/master/classes',
                roles: ['admin', 'principal'],
            },
            {
                title: 'Data Guru',
                href: '/master/teachers',
                roles: ['admin', 'principal'],
            },
            {
                title: 'Jadwal Pelajaran',
                href: '/master/schedules',
                roles: ['admin', 'principal'],
            },
            {
                title: 'Data Pelajaran',
                href: '/master/subjects',
                roles: ['admin', 'principal'],
            },
            {
                title: 'Data Tahun Ajaran',
                href: '/master/academic-years',
                roles: ['admin', 'principal'],
            },
        ],
    },
];

function filterItems(items: NavItem[]): NavItem[] {
    const role = userRole.value;
    if (!role) return []; // If no role, show nothing (or maybe show public items if any)

    return items.reduce<NavItem[]>((acc, item) => {
        // Check filtering
        if (item.roles && !item.roles.includes(role)) {
            return acc;
        }

        const newItem = { ...item };

        if (item.items) {
            const children = filterItems(item.items);
             if (children.length > 0) {
                newItem.items = children;
            } else {
                // If this item was supposed to be a parent but has no visible children,
                // and it has no href of its own (or duplicates href), handle accordingly.
                // In our case, 'Attendance' has href '/attendance' and is valid as standalone.
                // 'Master Data' has href '/master' but filtering removes it entirely anyway if roles don't match.
                // But if roles match Parent but NO Children?
                // Example: Teacher sees 'Attendance'. 
                // Children: 'Presence'.
                // If 'Presence' was also removed, 'Attendance' would have no children.
                // NavMain would render it as a Link. This is correct behavior.
                // So we just assign undefined or delete property.
                delete newItem.items; 
            }
        }

        acc.push(newItem);
        return acc;
    }, []);
}

const mainNavItems = computed(() => filterItems(allNavItems));
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
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
