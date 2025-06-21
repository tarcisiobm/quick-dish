import axios from 'axios'

const api = axios.create({
  baseURL: process.env.VUE_APP_BACKEND_API,
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
  },
})

export default api
