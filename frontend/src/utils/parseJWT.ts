interface IJwt {
    aud: string;
    exp: number;
    flow: string;
    iat: number;
    iss: string;
    sub: string;
    uuid: string;
}

export const parseJWT = (token: string | undefined): IJwt | undefined => {
    try {
        if (token) {
            const parsedToken = JSON.parse(atob(token.split('.')[1]));
            return parsedToken;
        }
        return undefined;
    } catch (e) {
        return undefined;
    }
};
