import axios from 'axios';
import { reactive, computed } from 'vue';

const token = localStorage.getItem('token');
export const authState = reactive({
    user: null,
    token: token && token !== 'null' && token !== 'undefined' ? token : null,
    isAuthenticated: !!(token && token !== 'null' && token !== 'undefined')
});

export const isAdmin = computed(() => authState.user?.is_admin === 1 || authState.user?.is_admin === true);

if (authState.token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${authState.token}`;
}

export const login = async (credentials) => {
    try {
        const response = await axios.post('/api/login', credentials);
        const { user, token } = response.data;
        authState.user = user;
        authState.token = token;
        authState.isAuthenticated = true;
        localStorage.setItem('token', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        return { success: true };
    } catch (error) {
        return { success: false, message: error.response?.data?.message || 'Login failed' };
    }
};

export const register = async (userData) => {
    try {
        await axios.post('/api/register', userData);
        return { success: true };
    } catch (error) {
        return { success: false, message: error.response?.data?.message || 'Registration failed' };
    }
};

export const logout = async () => {
    try {
        await axios.post('/api/logout');
    } catch (error) {
        console.error('Logout error', error);
    } finally {
        authState.user = null;
        authState.token = null;
        authState.isAuthenticated = false;
        localStorage.removeItem('token');
        delete axios.defaults.headers.common['Authorization'];
    }
};

export const fetchUser = async () => {
    if (!authState.token) return;
    try {
        const response = await axios.get('/api/user');
        authState.user = response.data;
    } catch (error) {
        logout();
    }
};
