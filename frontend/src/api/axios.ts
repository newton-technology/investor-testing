import axios from 'axios';

import AccessTokenStorage from '../stores/AccessTokenStorage';

const axiosWithToken = axios.create();

axiosWithToken.interceptors.request.use(
    (config) => {
        const token = AccessTokenStorage.accessToken;
        if (token) {
            config.headers['Authorization'] = `Bearer ${token}`;
        } else {
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    },
);

export default axiosWithToken;
