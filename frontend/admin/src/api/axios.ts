import axios from 'axios';
import qs from 'qs';

import {accessTokenStorage} from '../stores/AccessTokenStorage';

const axiosWithToken = axios.create();

axiosWithToken.interceptors.request.use(
    (config) => {
        const token = accessTokenStorage.accessToken;
        config.paramsSerializer = (params: string) => qs.stringify(params, {arrayFormat: 'indices'});

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
