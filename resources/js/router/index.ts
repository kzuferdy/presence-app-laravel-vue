import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import Dashboard from '../pages/Dashboard.vue';

const routes: RouteRecordRaw[] = [
    {
        path: '/',
        name: 'home',
        redirect: '/dashboard',
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
    },
    {
        path: '/attendance',
        redirect: '/attendance/students', // Default to students, or handle based on role in component
        children: [
            {
                path: 'students',
                name: 'students.index',
                component: () => import('../pages/Attendance/Students/Index.vue'),
            },
            {
                path: 'students/create',
                name: 'students.create',
                component: () => import('../pages/Attendance/Students/FormCreate.vue'),
            },
            {
                path: 'students/:id/edit',
                name: 'students.edit',
                component: () => import('../pages/Attendance/Students/FormEdit.vue'),
            },
            {
                path: 'presence',
                name: 'presence.index',
                component: () => import('../pages/Attendance/Presence/Index.vue'),
            },
            {
                path: 'presence/create',
                name: 'presence.create',
                component: () => import('../pages/Attendance/Presence/Create.vue'),
            },
            {
                path: 'recap',
                name: 'recap.index',
                component: () => import('../pages/Attendance/Recap/Index.vue'),
            },
        ],
    },
    {
        path: '/master',
        redirect: '/master/classes',
        children: [
            {
                path: 'classes',
                name: 'classes.index',
                component: () => import('../pages/Master/Classes/Index.vue'),
            },
            {
                path: 'academic-years',
                name: 'academic-years.index',
                component: () => import('../pages/Master/AcademicYears/Index.vue'),
            },
        ],
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../pages/auth/Login.vue'),
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../pages/auth/Register.vue'),
    },
    {
        path: '/profile',
        name: 'profile.edit',
        component: () => import('../pages/settings/Profile.vue'),
    },
    {
        path: '/settings/password',
        name: 'settings.password',
        component: () => import('../pages/settings/Password.vue'),
    },
    {
        path: '/settings/two-factor',
        name: 'settings.two-factor',
        component: () => import('../pages/settings/TwoFactor.vue'),
    },
    {
        path: '/settings/appearance',
        name: 'settings.appearance',
        component: () => import('../pages/settings/Appearance.vue'),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
