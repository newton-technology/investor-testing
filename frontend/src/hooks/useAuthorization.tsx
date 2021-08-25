import React, {useEffect} from 'react';

import {authService} from '../api/AuthService';
import {accessTokenStorage} from '../stores/AccessTokenStorage';
import {useIsAfk} from './useIsAfk';

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
    const {isAfk} = useIsAfk();
    accessTokenStorage.subscribe(setUserToken);

    useEffect(() => {
        setisAuthenticated(accessTokenStorage.isAuthenticated);
    }, [userToken]);

    useEffect(() => {
        if (isAfk) {
            authService.logout();
        }
    }, [isAfk]);

    return {userToken, isAuthenticated};
};
