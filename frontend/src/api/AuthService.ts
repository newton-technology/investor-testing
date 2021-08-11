import axios from 'axios';

import AccessTokenStorage from '../stores/AccessTokenStorage';

axios.interceptors.request.use(
    (config) => {
        const token = AccessTokenStorage.token;
        if (token) {
            config.headers['Authorization'] = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    },
);
class AuthService {
    private readonly url = `${process.env.REACT_APP_API_URL}/authorization`;

    public async login(payload: any) {
        const accessToken = await axios.post(`${this.url}/signin`, payload).catch(() => {
            /* */
        });
        if (!!accessToken.data) {
            AccessTokenStorage.token = accessToken.data;
        }
    }

    public logout() {
        return this;
    }

    public refresh() {
        return this;
    }
}

export default new AuthService();
