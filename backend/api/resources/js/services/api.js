import axios from 'axios';

// Create axios instance
const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Accept': 'application/json',
  },
});

// Helper: attach token if present in localStorage
export function setAuthHeader(token) {
  if (token) {
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
  } else {
    delete api.defaults.headers.common['Authorization'];
  }
}

// Initialize with token from localStorage (if any)
setAuthHeader(localStorage.getItem('token') || null);

// Response interceptor for simple global error handling (optional)
api.interceptors.response.use(
  (res) => res,
  (error) => {
    // Could add global handling (401 logout) here
    return Promise.reject(error);
  }
);

export default api;
