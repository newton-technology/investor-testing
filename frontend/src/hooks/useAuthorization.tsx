import React, {useEffect} from 'react';

import {accessTokenStorage} from '../stores/AccessTokenStorage';

interface IToken {
    accesToken?: string;
    refreshToken?: string;
}

interface ITokenUserInfo {
    userToken: IToken;
    isAuthenticated: boolean;
}

export const useAuthorization = (): ITokenUserInfo => {
    const [userToken, setUserToken] = React.useState<IToken>({});
    const [isAuthenticated, setisAuthenticated] = React.useState<boolean>(false);

    accessTokenStorage.subscribe(setUserToken);

    useEffect(() => {
        setisAuthenticated(accessTokenStorage.isAuthenticated);
    }, [userToken]);

    return {userToken, isAuthenticated};
};
