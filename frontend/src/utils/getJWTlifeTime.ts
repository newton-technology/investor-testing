import {parseJWT} from './parseJwt';

const TRESHOLD_OF_TOKEN_LIFE_IN_PERCENTAGE = 0.15;

export const getJWTlifeTime = (token: string | undefined): number | undefined => {
    const parsedJwt = parseJWT(token);
    if (parsedJwt) {
        const {iat, exp} = parsedJwt;
        const tokenLifeTime = exp - iat;
        const refreshTime = tokenLifeTime - tokenLifeTime * TRESHOLD_OF_TOKEN_LIFE_IN_PERCENTAGE;
        return refreshTime;
    }

    return undefined;
};
