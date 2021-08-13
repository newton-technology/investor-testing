import React, {useEffect} from 'react';

import AccessTokenStorage from '../stores/AccessTokenStorage';

interface IToken {
    accesToken?: string;
    refreshToken?: string;
}

interface ITokenUserInfo {
    userToken: IToken;
    isAuth: boolean;
}

export const useAuthorization = (): ITokenUserInfo => {
    const [userToken, setUserToken] = React.useState<IToken>({});
    const [isAuth, setIsAuth] = React.useState<boolean>(false);

    AccessTokenStorage.subscribe(setUserToken);

    useEffect(() => {
        setIsAuth(AccessTokenStorage.isAuth);
    }, [userToken]);

    return {userToken, isAuth};
};
