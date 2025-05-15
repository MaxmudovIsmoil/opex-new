import axios from 'axios';
import Cookies from 'js-cookie';

axios.defaults.withCredentials = true; 

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_APP_URL +"/api", 
  // baseURL: 'http://127.0.0.1:8000/api', // Bazaviy URL
  headers: {
    'Content-Type': 'application/json',
    // 'Authorization': `Bearer ${localStorage.getItem('token')}`
  },
  withCredentials: true
});

// So‘rovlar uchun interceptor (ixtiyoriy)
apiClient.interceptors.request.use(
  (config) => {
    const token = Cookies.get("token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    
    // Har bir so‘rov oldidan qo‘shimcha logika (masalan, token qo‘shish)
    return config;
  },
  (error) => Promise.reject(error)
);

// Javoblar uchun interceptor (ixtiyoriy)
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    // Xatoliklarni boshqarish (masalan, 401 uchun logout)
    return Promise.reject(error);
  }
);

export default apiClient;