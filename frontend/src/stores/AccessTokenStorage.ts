interface IListener {
    key: string;
}

class AccessTokenStorage {
    private _accessToken: string | undefined;
    private _refreshToken: string | undefined;
    private listener: any;

    constructor() {
        this._accessToken = undefined;
        this.init();
    }

    public get accessToken(): string | undefined {
        return this._accessToken;
    }

    public set accessToken(token: string | undefined) {
        if (!!token) {
            this._accessToken = token;
            localStorage.setItem('accessToken', token);
            this.listener({accessToken: this._accessToken, refreshToken: undefined});
        }
    }

    public get refreshToken(): string | undefined {
        return this._refreshToken;
    }

    public set refreshToken(token: string | undefined) {
        if (!!token) {
            this._refreshToken = token;
            localStorage.setItem('refreshToken', token);
            this.listener({accessToken: this._accessToken, refreshToken: this._refreshToken});
        }
    }

    public onChange(cb: any) {
        this.listener = cb;
    }

    public get isAuth(): boolean {
        return !!this._refreshToken;
    }

    private init() {
        const accessToken = localStorage.getItem('accessToken');
        if (accessToken) {
            this._accessToken = accessToken;
        }
    }
}

export default new AccessTokenStorage();
