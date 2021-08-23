import {accessTokenStorage} from '../stores/AccessTokenStorage';
import {getJWTRefreshTime} from '../utils/jwtUtils';
import axios from './axios';

interface IServerResponse {
    access_token: string;
    refresh_token?: string;
}

interface IBaseAuth {
    email: string;
    grant_type: 'code';
}

interface ILogin extends IBaseAuth {
    code: string;
    access_token: string;
}

interface IRefresh {
    refresh_token?: string;
    grant_type: 'refresh_token';
}

class AuthService {
    private readonly url = `${process.env.REACT_APP_API_URL}/authorization`;
    private watcher: number | undefined = undefined;

    constructor() {
        this.startRefreshTokenWatcher();
    }

    public async login(payload: ILogin) {
        const data = await this.request('token', payload);
        this.setToken(data);
    }

    public async sendCode(payload: IBaseAuth) {
        const data = await this.request('signin', payload);
        this.setToken(data);
    }

    public logout() {
        accessTokenStorage.accessToken = undefined;
        accessTokenStorage.refreshToken = undefined;
    }

    public async refresh(payload: IRefresh) {
        const data = await this.request('token', payload);
        this.setToken(data);
    }

    private setToken(data: IServerResponse | undefined) {
        if (data?.access_token) {
            accessTokenStorage.accessToken = data.access_token;
        }
        if (data?.refresh_token) {
            accessTokenStorage.refreshToken = data.refresh_token;
            this.startRefreshTokenWatcher();
        }
    }

    private async request(endpoint: string, payload: any): Promise<IServerResponse | undefined> {
        try {
            const {data} = await axios.post<IServerResponse>(`${this.url}/${endpoint}`, payload);
            return data;
        } catch (e) {
            console.log(e);
        }
    }

    private startRefreshTokenWatcher() {
        if (this.watcher) {
            clearTimeout(this.watcher);
        }

        if (accessTokenStorage.refreshToken) {
            const time = getJWTRefreshTime(accessTokenStorage?.refreshToken, Date.now());
            this.watcher = window.setTimeout(() => {
                this.refresh({
                    refresh_token: accessTokenStorage.refreshToken,
                    grant_type: 'refresh_token',
                });
            }, time);
        }
    }
}

export const authService = new AuthService();
