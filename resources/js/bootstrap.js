import axios from 'axios';
window.axios = axios;

// Use base URL from meta tag or construct from current location
const metaBaseUrl = document.querySelector('meta[name="api-base-url"]')?.getAttribute('content');
if (metaBaseUrl) {
    axios.defaults.baseURL = metaBaseUrl;
} else {
    // Construct base URL from current location
    const baseUrl = window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/');
    axios.defaults.baseURL = baseUrl + '/api/v1';
}
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';

// Set auth token if exists
const token = localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Response interceptor for handling errors
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // Unauthorized - clear token and redirect to login
            localStorage.removeItem('token');
            delete axios.defaults.headers.common['Authorization'];
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

console.log('Axios configured with baseURL:', axios.defaults.baseURL);
