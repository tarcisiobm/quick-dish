import axios from 'axios';

const api = axios.create({
  baseURL: process.env.VUE_APP_BACKEND_API,
  timeout: 10000,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

export default api;
