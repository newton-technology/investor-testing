import AccessTokenStorage from '../stores/AccessTokenStorage';
import {parseJwtRefreshTime} from '../utils/parseJwtLifeTime';
import axios from './axios';

interface IServerResponse {
    accessToken: string;
    refreshToken?: string;
}

interface IBaseAuth {
    email: string;
    grant_type: 'code';
}

interface ILogin extends IBaseAuth {
    code: string;
    accessToken: string;
}

interface IRefresh {
    refreshToken?: string;
    grant_type: 'refresh_token';
}

class AuthService {
    private readonly url = `${process.env.REACT_APP_API_URL}/authorization`;
    private watcher: number | undefined = undefined;

    public async login(payload: ILogin) {
        const data = await this.request('token', payload);
        this.setToken(data);
    }

    public async sendCode(payload: IBaseAuth) {
        const data = await this.request('signup', payload);
        this.setToken(data);
    }

    public logout() {
        AccessTokenStorage.accessToken = undefined;
        AccessTokenStorage.refreshToken = undefined;
    }

    public async refresh(payload: IRefresh) {
        const data = await this.request('token', payload);
        this.setToken(data);
    }

    private setToken(data: IServerResponse | undefined) {
        if (data?.accessToken) {
            AccessTokenStorage.accessToken = data.accessToken;
        }
        if (data?.refreshToken) {
            AccessTokenStorage.refreshToken = data.refreshToken;
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
        const time = parseJwtRefreshTime(AccessTokenStorage?.refreshToken);
        this.watcher = window.setTimeout(() => {
            this.refresh({
                refreshToken: AccessTokenStorage.refreshToken,
                grant_type: 'refresh_token',
            });
        }, time);
    }
}

export default new AuthService();
