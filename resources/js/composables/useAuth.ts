import { ref, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import type { User } from '@/types';

const user = ref<User | null>(null);
const isLoading = ref(false);

export function useAuth() {
    const router = useRouter();

    const fetchUser = async () => {
        if (user.value) return;
        isLoading.value = true;
        try {
            const response = await axios.get('/api/user');
            user.value = response.data;
        } catch (error) {
            console.error('Failed to fetch user', error);
            user.value = null;
        } finally {
            isLoading.value = false;
        }
    };

    const logout = async () => {
        try {
            await axios.post('/api/logout');
        } catch (error) {
            console.error('Logout failed', error);
        } finally {
            user.value = null;
            localStorage.removeItem('token');
            window.location.href = '/login';
        }
    };

    return {
        user: computed(() => user.value),
        isLoading: computed(() => isLoading.value),
        fetchUser,
        logout,
    };
}
