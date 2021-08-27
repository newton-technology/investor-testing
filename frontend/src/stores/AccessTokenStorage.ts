import {isJwtExpired} from '../utils/jwtUtils';

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
            this.sendTokenToSubscriber();
        } else {
            this._accessToken = token;
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
        } else {
            this._refreshToken = token;
            localStorage.removeItem('refreshToken');
            this.sendTokenToSubscriber();
        }
    }

    public subscribe(cb: (...args: any) => void) {
        this.subscriber = cb;
    }

    public get isAuthenticated(): boolean {
        return !!this._refreshToken && !this.isRefreshTokenExpired && !!this._accessToken;
    }

    public get isRefreshTokenExpired(): boolean {
        return isJwtExpired(this._refreshToken, Date.now());
    }

    private sendTokenToSubscriber() {
        if (this.subscriber) {
            this.subscriber({accessToken: this._accessToken, refreshToken: this._refreshToken});
        }
    }

    private init() {
        const refreshToken = localStorage.getItem('refreshToken');

        if (refreshToken) {
            this._refreshToken = refreshToken;
        }
    }
}

export const accessTokenStorage = new AccessTokenStorage();
