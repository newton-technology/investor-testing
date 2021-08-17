import axios from 'axios';

import {accessTokenStorage} from '../stores/AccessTokenStorage';

const axiosWithToken = axios.create();

axiosWithToken.interceptors.request.use(
    (config) => {
        const token = accessTokenStorage.accessToken;
        if (token) {
            config.headers['Authorization'] = `Bearer ${token}`;
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    },
);

export default axiosWithToken;
