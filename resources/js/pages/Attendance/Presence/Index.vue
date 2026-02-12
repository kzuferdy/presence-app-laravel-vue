<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import * as faceapi from 'face-api.js';

type Attendance = {
    id: number;
    status: 'present' | 'sick' | 'permission' | 'alpha';
};

type Student = {
    id: number;
    name: string;
    nis: string;
    class: string;
    photo: string | null;
    attendances: Attendance[];
};

const students = ref<Student[]>([]);
const date = ref<string>('');
const loading = ref<number | null>(null);
const videoRef = ref<HTMLVideoElement | null>(null);
const canvasRef = ref<HTMLCanvasElement | null>(null);
const isModelLoaded = ref(false);
const isVideoStarted = ref(false);
const faceMatcher = ref<faceapi.FaceMatcher | null>(null);
let detectionInterval: NodeJS.Timeout | null = null;

const fetchData = async () => {
    try {
        const response = await axios.get('/api/attendance');
        students.value = response.data.students;
        date.value = response.data.date;
        
        // After fetching students, prepare face descriptors
        if (isModelLoaded.value) {
            await prepareFaceMatcher();
        }
    } catch (error) {
        console.error('Failed to fetch attendance data:', error);
    }
};

const loadModels = async () => {
    try {
        const MODEL_URL = '/models';
        console.log('Loading models from:', MODEL_URL);
        await Promise.all([
            faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
        ]);
        isModelLoaded.value = true;
        console.log('Models loaded successfully');
        if (students.value.length > 0) {
            await prepareFaceMatcher();
        }
    } catch (error) {
        console.error('Error loading models:', error);
    }
};

const prepareFaceMatcher = async () => {
    if (!students.value.length) {
        console.log('No students to match');
        return;
    }

    const labeledFaceDescriptors: faceapi.LabeledFaceDescriptors[] = [];
    const studentsWithPhotos = students.value.filter(s => s.photo);
    console.log(`Found ${studentsWithPhotos.length} students with photos out of ${students.value.length}`);

    for (const student of studentsWithPhotos) {
        try {
            const imgUrl = `/storage/${student.photo}`;
            console.log(`Processing photo for ${student.name}: ${imgUrl}`);
            const img = await faceapi.fetchImage(imgUrl);
            const detections = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();
            
            if (detections) {
                labeledFaceDescriptors.push(new faceapi.LabeledFaceDescriptors(student.name, [detections.descriptor]));
                console.log(`Face descriptor generated for ${student.name}`);
            } else {
                console.warn(`No face detected in photo for ${student.name}`);
            }
        } catch (error) {
            console.warn(`Failed to process photo for ${student.name}:`, error);
        }
    }

    if (labeledFaceDescriptors.length > 0) {
        faceMatcher.value = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.6);
        console.log('Face matcher ready with', labeledFaceDescriptors.length, 'descriptors');
    } else {
        console.warn('No valid face descriptors generated.');
    }
};

const startVideo = async () => {
    if (!videoRef.value) return;

    try {
        console.log('Requesting camera access...');
        const stream = await navigator.mediaDevices.getUserMedia({ video: {} });
        videoRef.value.srcObject = stream;
        isVideoStarted.value = true;
        console.log('Camera started');
    } catch (err) {
        console.error('Error starting video:', err);
        alert('Failed to access camera. Please allow camera permissions.');
    }
};

const onVideoPlay = () => {
    const video = videoRef.value;
    const canvas = canvasRef.value;
    if (!video || !canvas ) {
        console.error('Video or canvas element missing');
        return;
    }
    
    if (!faceMatcher.value) {
        console.warn('Face matcher not ready yet. Waiting...');
    }

    console.log('Video playing, starting detection loop');
    const displaySize = { width: video.videoWidth, height: video.videoHeight };
    faceapi.matchDimensions(canvas, displaySize);

    detectionInterval = setInterval(async () => {
        if (!video || !canvas) return;
        if (video.paused || video.ended) return;

        // Ensure canvas matches video dimensions
        if (canvas.width !== video.videoWidth || canvas.height !== video.videoHeight) {
             const displaySize = { width: video.videoWidth, height: video.videoHeight };
             faceapi.matchDimensions(canvas, displaySize);
        }

        const detections = await faceapi.detectAllFaces(video).withFaceLandmarks().withFaceDescriptors();
        const resizeDetections = faceapi.resizeResults(detections, displaySize);

        const context = canvas.getContext('2d');
        if (context) {
            context.clearRect(0, 0, canvas.width, canvas.height);
        }

        if (faceMatcher.value) {
            const results = resizeDetections.map(d => faceMatcher.value!.findBestMatch(d.descriptor));
            
            results.forEach((result, i) => {
                const box = resizeDetections[i].detection.box;
                const drawBox = new faceapi.draw.DrawBox(box, { label: result.toString() });
                drawBox.draw(canvas);
            });
        }
    }, 100);
};

onMounted(async () => {
    await loadModels();
    await fetchData();
    startVideo();
});

onUnmounted(() => {
    if (detectionInterval) clearInterval(detectionInterval);
    if (videoRef.value && videoRef.value.srcObject) {
        const stream = videoRef.value.srcObject as MediaStream;
        stream.getTracks().forEach(track => track.stop());
    }
});

const setStatus = async (studentId: number, status: string) => {
    loading.value = studentId;
    try {
        await axios.post('/api/attendance', {
            student_id: studentId,
            date: date.value,
            status: status,
        });

        const student = students.value.find(s => s.id === studentId);
        if (student) {
            if (student.attendances.length > 0) {
                student.attendances[0].status = status as any;
            } else {
                student.attendances.push({
                    id: 0,
                    status: status as any
                });
            }
        }
    } catch (error) {
        console.error('Failed to update status:', error);
    } finally {
        loading.value = null;
    }
};

const getStatus = (student: Student) => {
    return student.attendances.length ? student.attendances[0].status : null;
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Attendance', href: '/attendance' },
    { title: 'Presence', href: '/attendance/presence' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-8">
            <div class="flex flex-col gap-1">
                <h1 class="text-2xl font-semibold tracking-tight">Daily Presence</h1>
                <p class="text-sm text-muted-foreground">Mark attendance for {{ date }}</p>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Camera Section -->
                <div class="rounded-xl border bg-card p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-medium">Face Recognition</h3>
                    <div class="relative overflow-hidden rounded-lg bg-black aspect-video">
                        <div v-if="!isModelLoaded" class="absolute inset-0 flex items-center justify-center text-white">
                            Loading models...
                        </div>
                        <video
                            ref="videoRef"
                            class="absolute inset-0 h-full w-full object-cover"
                            autoplay
                            muted
                            @play="onVideoPlay"
                        ></video>
                        <canvas ref="canvasRef" class="absolute inset-0 h-full w-full"></canvas>
                    </div>
                </div>

                <!-- Manual Attendance Section -->
                <div class="rounded-xl border bg-card p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-medium">Student List</h3>
                    <div v-if="students.length" class="overflow-x-auto">
                        <table class="w-full border-collapse text-sm">
                            <thead>
                                <tr class="border-b text-left text-muted-foreground">
                                    <th class="py-2 px-2">Name</th>
                                    <th class="py-2 px-2 text-center">Status</th>
                                    <th class="py-2 px-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="student in students"
                                    :key="student.id"
                                    class="border-b last:border-0 hover:bg-muted/50"
                                >
                                    <td class="py-3 px-2 font-medium">{{ student.name }}</td>
                                    <td class="py-3 px-2 text-center">
                                        <span v-if="getStatus(student)" 
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium capitalize"
                                            :class="{
                                                'bg-green-100 text-green-800': getStatus(student) === 'present',
                                                'bg-blue-100 text-blue-800': getStatus(student) === 'sick',
                                                'bg-yellow-100 text-yellow-800': getStatus(student) === 'permission',
                                                'bg-red-100 text-red-800': getStatus(student) === 'alpha',
                                            }"
                                        >
                                            {{ getStatus(student)?.charAt(0).toUpperCase() }}
                                        </span>
                                        <span v-else class="text-muted-foreground">-</span>
                                    </td>
                                    <td class="py-3 px-2 text-right">
                                        <div class="flex justify-end gap-1">
                                            <button @click="setStatus(student.id, 'present')" class="rounded px-2 py-1 text-xs font-medium hover:bg-green-200" :class="getStatus(student) === 'present' ? 'bg-green-200 text-green-900' : 'bg-green-100 text-green-800'" :disabled="loading === student.id">P</button>
                                            <button @click="setStatus(student.id, 'sick')" class="rounded px-2 py-1 text-xs font-medium hover:bg-blue-200" :class="getStatus(student) === 'sick' ? 'bg-blue-200 text-blue-900' : 'bg-blue-100 text-blue-800'" :disabled="loading === student.id">S</button>
                                            <button @click="setStatus(student.id, 'permission')" class="rounded px-2 py-1 text-xs font-medium hover:bg-yellow-200" :class="getStatus(student) === 'permission' ? 'bg-yellow-200 text-yellow-900' : 'bg-yellow-100 text-yellow-800'" :disabled="loading === student.id">I</button>
                                            <button @click="setStatus(student.id, 'alpha')" class="rounded px-2 py-1 text-xs font-medium hover:bg-red-200" :class="getStatus(student) === 'alpha' ? 'bg-red-200 text-red-900' : 'bg-red-100 text-red-800'" :disabled="loading === student.id">A</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
