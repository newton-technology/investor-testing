interface IJwt {
    aud: string;
    exp: number;
    flow: string;
    iat: number;
    iss: string;
    sub: string;
    uuid: string;
}

// refresh token at 20% of lifetime
const TRESHOLD_OF_TOKEN_LIFE_IN_PERCENTAGE = 0.2;

/**
 *  count ms for timeout watcher
 * @param token
 * @param dateNow
 * @returns
 */

export const getJWTRefreshTime = (token: string | undefined, dateNow: number): number | undefined => {
    const parsedJwt = parseJWT(token);
    if (parsedJwt) {
        const {iat, exp} = parsedJwt;
        const tokenLifeTime = exp - iat;
        const refreshTime = tokenLifeTime - tokenLifeTime * TRESHOLD_OF_TOKEN_LIFE_IN_PERCENTAGE;
        const tokenEndLife = (iat + refreshTime) * 1000;
        const timeout = tokenEndLife - dateNow;
        return Math.round(timeout);
    }

    return undefined;
};

export const isJwtExpired = (token: string | undefined, dateNow: number): boolean => {
    const parsedJwt = parseJWT(token);
    if (parsedJwt) {
        const {exp} = parsedJwt;
        return exp * 1000 < dateNow;
    }

    return false;
};

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
