import AccessTokenStorage from '../stores/AccessTokenStorage';
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

interface ITokenStep extends IBaseAuth {
    code: string;
    accessToken: string;
}

class AuthService {
    private readonly url = `${process.env.REACT_APP_API_URL}/authorization`;

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

    public refresh() {
        return this;
    }

    private setToken(data: IServerResponse | undefined) {
        if (data?.accessToken) {
            AccessTokenStorage.accessToken = data.accessToken;
        }
        if (data?.refreshToken) {
            AccessTokenStorage.refreshToken = data.refreshToken;
        }
    }

    private async confirmStep(payload: ITokenStep) {
        await this.request('signin', payload);
    }

    private async request(endpoint: string, payload: any): Promise<IServerResponse | undefined> {
        try {
            const {data} = await axios.post<IServerResponse>(`${this.url}/${endpoint}`, payload);
            return data;
        } catch (e) {
            console.log(e);
        }
    }
}

export default new AuthService();
