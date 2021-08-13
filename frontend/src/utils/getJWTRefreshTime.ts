import {parseJWT} from './parseJWT';

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

// TODO: Rename
export const isJWTActual = (token: string | undefined, dateNow: number): boolean => {
    const parsedJwt = parseJWT(token);
    if (parsedJwt) {
        const {exp} = parsedJwt;
        return exp * 1000 > dateNow;
    }

    return false;
};
