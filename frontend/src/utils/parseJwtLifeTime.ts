const TRESHOLD_OF_TOKEN_LIFE_IN_PERCENTAGE = 0.15;

export const parseJwtRefreshTime = (token: string | undefined): number | undefined => {
    try {
        if (token) {
            const {iat, exp} = JSON.parse(atob(token.split('.')[1]));
            const tokenLifeTime = iat - exp;
            const refreshTime = tokenLifeTime - tokenLifeTime * TRESHOLD_OF_TOKEN_LIFE_IN_PERCENTAGE;

            return refreshTime;
        }
        return undefined;
    } catch (e) {
        return undefined;
    }
};
