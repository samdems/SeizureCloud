import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add CSRF token to all requests
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Handle any global axios errors
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 419) {
            console.error('Session expired. Please refresh the page.');
        }
        return Promise.reject(error);
    }
);
