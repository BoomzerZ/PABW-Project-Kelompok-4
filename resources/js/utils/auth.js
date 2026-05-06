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

// Ensure Authorization is attached for every request (covers refresh and hot reload cases).
axios.interceptors.request.use((config) => {
    const storedToken = localStorage.getItem('token');
    if (storedToken && storedToken !== 'null' && storedToken !== 'undefined') {
        config.headers = config.headers || {};
        config.headers.Authorization = `Bearer ${storedToken}`;
    }
    return config;
});

// Reset auth state on 401 responses to avoid stale login state.
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            authState.user = null;
            authState.token = null;
            authState.isAuthenticated = false;
            localStorage.removeItem('token');
            delete axios.defaults.headers.common['Authorization'];
        }
        return Promise.reject(error);
    }
);

export const login = async (credentials) => {
    try {
        const response = await axios.post('/api/login', credentials);
        const token = response.data.access_token;
        authState.token = token;
        authState.isAuthenticated = true;
        localStorage.setItem('token', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        await fetchUser();
        return { success: true };
    } catch (error) {
        return { success: false, message: error.response?.data?.message || 'Login failed' };
    }
};

export const register = async (userData) => {
    try {
        const response = await axios.post('/api/register', userData);
        const token = response.data.access_token;
        authState.token = token;
        authState.isAuthenticated = true;
        localStorage.setItem('token', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        await fetchUser();
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
