import {isJWTActual} from './../utils/getJWTRefreshTime';
class AccessTokenStorage {
    private _accessToken: string | undefined;
    private _refreshToken: string | undefined;
    private subscriber: ((...args: any) => void) | undefined;

    constructor() {
        this.init();
    }

    public get accessToken(): string | undefined {
        return this._accessToken;
    }

    public set accessToken(token: string | undefined) {
        if (token) {
            this._accessToken = token;
            localStorage.setItem('accessToken', token);
            this.sendTokenToSubscriber();
        }
    }

    public get refreshToken(): string | undefined {
        return this._refreshToken;
    }

    public set refreshToken(token: string | undefined) {
        if (token) {
            this._refreshToken = token;
            localStorage.setItem('refreshToken', token);
            this.sendTokenToSubscriber();
        }
    }

    public subscribe(cb: (...args: any) => void) {
        this.subscriber = cb;
    }

    public get isAuth(): boolean {
        return !!this._refreshToken && isJWTActual(this._refreshToken, Date.now());
    }

    private sendTokenToSubscriber() {
        if (this.subscriber) {
            this.subscriber({accessToken: this._accessToken, refreshToken: this._refreshToken});
        }
    }

    private init() {
        const accessToken = localStorage.getItem('accessToken');
        const refreshToken = localStorage.getItem('refreshToken');

        if (accessToken) {
            this._accessToken = accessToken;
        }

        if (refreshToken) {
            this._refreshToken = refreshToken;
        }
    }
}

export default new AccessTokenStorage();
