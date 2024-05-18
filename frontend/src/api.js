// src/api.js
import axios from 'axios';

const apiClient = axios.create({
  baseURL: 'http://localhost:your-backend-port/api',
  headers: {
    'Content-Type': 'application/json'
  }
});

export default apiClient;
