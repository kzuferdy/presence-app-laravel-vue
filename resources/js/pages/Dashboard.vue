<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { GraduationCap, Users, School, BookOpen } from 'lucide-vue-next';

interface DashboardStats {
    active_academic_year: string;
    total_classes: number;
    total_students: number;
}

const stats = ref<DashboardStats>({
    active_academic_year: 'Loading...',
    total_classes: 0,
    total_students: 0,
});

const error = ref<string | null>(null);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Greeting dinamis
const greeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Selamat Pagi';
    if (hour < 18) return 'Selamat Siang';
    return 'Selamat Malam';
};

onMounted(async () => {
    try {
        const response = await axios.get('/api/dashboard');
        stats.value = response.data;
    } catch (err) {
        console.error('Failed to fetch dashboard stats', err);
        error.value = 'Gagal memuat data dashboard.';
        if (axios.isAxiosError(err)) {
            error.value += ` (${err.message})`;
        }
    }
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">

            <!-- Error -->
            <div v-if="error" class="rounded-md bg-red-50 p-4 text-sm text-red-500">
                {{ error }}
            </div>

            <!-- 🔥 HERO SECTION -->
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-indigo-600 via-indigo-500 to-purple-600 p-8 text-white shadow-lg">

                <!-- Overlay -->
                <div class="absolute inset-0 bg-black/10"></div>

                <div class="relative z-10 max-w-3xl">
                    <h1 class="text-3xl font-bold tracking-tight mb-2">
                        Selamat Datang di Sistem Absensi Sekolah Garuda
                    </h1>

                    <p class="text-indigo-100 leading-relaxed">
                        Sekolah Garuda adalah institusi pendidikan yang berkomitmen mencetak 
                        generasi unggul, berkarakter, dan berdaya saing tinggi melalui 
                        pembelajaran inovatif dan sistem absensi digital terintegrasi.
                    </p>

                    <div class="mt-4 flex flex-wrap gap-4 text-sm text-indigo-200">
                        <span>📍 Sumedang</span>
                        <span>🎓 Pendidikan Berkualitas</span>
                        <span>🚀 Generasi Unggul</span>
                    </div>
                </div>

                <!-- Background Icon -->
                <div class="absolute right-8 bottom-0 text-white/10">
                    <BookOpen :size="140" />
                </div>
            </div>

            <!-- 🔥 QUICK INFO -->
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-lg bg-indigo-50 p-4 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-300">
                    📊 Sistem absensi real-time
                </div>
                <div class="rounded-lg bg-green-50 p-4 text-green-700 dark:bg-green-900/20 dark:text-green-300">
                    👨‍🎓 Monitoring kehadiran siswa
                </div>
                <div class="rounded-lg bg-purple-50 p-4 text-purple-700 dark:bg-purple-900/20 dark:text-purple-300">
                    📚 Terintegrasi dengan data akademik
                </div>
            </div>

            <!-- 🔥 STATS -->
            <div class="grid gap-6 md:grid-cols-3">

                <!-- Academic Year -->
                <div class="rounded-xl border bg-card p-6 shadow-sm flex items-center gap-4 
                    hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                    
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                        <GraduationCap :size="32" />
                    </div>

                    <div>
                        <p class="text-sm font-medium text-muted-foreground">Tahun Ajaran</p>
                        <h3 class="text-2xl font-bold">{{ stats.active_academic_year }}</h3>
                        <p class="text-xs text-muted-foreground mt-1">
                            Data aktif saat ini
                        </p>
                    </div>
                </div>

                <!-- Total Classes -->
                <div class="rounded-xl border bg-card p-6 shadow-sm flex items-center gap-4 
                    hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                    
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                        <School :size="32" />
                    </div>

                    <div>
                        <p class="text-sm font-medium text-muted-foreground">Total Kelas</p>
                        <h3 class="text-2xl font-bold">{{ stats.total_classes }}</h3>
                        <p class="text-xs text-muted-foreground mt-1">
                            Semua kelas aktif
                        </p>
                    </div>
                </div>

                <!-- Total Students -->
                <div class="rounded-xl border bg-card p-6 shadow-sm flex items-center gap-4 
                    hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                    
                    <div class="p-3 rounded-full bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                        <Users :size="32" />
                    </div>

                    <div>
                        <p class="text-sm font-medium text-muted-foreground">Total Siswa</p>
                        <h3 class="text-2xl font-bold">{{ stats.total_students }}</h3>
                        <p class="text-xs text-muted-foreground mt-1">
                            Siswa terdaftar
                        </p>
                    </div>
                </div>
            </div>

            <!-- 🔥 QUICK ACTION -->
            <div class="rounded-xl border bg-card p-6 shadow-sm">
                <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>

                <div class="grid gap-4 md:grid-cols-3">

                    <a href="/attendance/presence/create"
                       class="rounded-lg border p-4 hover:bg-muted transition">
                        ✍️ Input Absensi
                    </a>

                    <a href="/attendance/recap"
                       class="rounded-lg border p-4 hover:bg-muted transition">
                        📊 Lihat Rekap
                    </a>

                    <a href="/master/classes"
                       class="rounded-lg border p-4 hover:bg-muted transition">
                        🏫 Kelola Kelas
                    </a>

                </div>
            </div>

        </div>
    </AppLayout>
</template>
