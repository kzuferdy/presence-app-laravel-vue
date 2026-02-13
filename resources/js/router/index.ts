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
                path: 'recap',
                name: 'recap.index',
                component: () => import('../pages/Attendance/Recap/Index.vue'),
            },
        ],
    },
    {
        path: '/master',
        children: [
            {
                path: 'classes',
                name: 'classes.index',
                component: () => import('../pages/Master/Classes/Index.vue'),
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
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
