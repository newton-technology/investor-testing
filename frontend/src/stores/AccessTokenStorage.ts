class AccessTokenStorage {
    private _token: string | undefined;

    constructor() {
        this._token = undefined;
        this.init();
    }

    public get token(): string | undefined {
        return this._token;
    }

    public set token(accessToken: string | undefined) {
        if (!!accessToken) {
            this._token = accessToken;
            localStorage.setItem('accessToken', accessToken);
        }
    }

    public get isAuth(): boolean {
        return !!this.token;
    }

    private init() {
        const accessToken = localStorage.getItem('accessToken');
        if (accessToken) {
            this._token = accessToken;
        }
    }
}

export default new AccessTokenStorage();
