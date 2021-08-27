import {useEffect, useState} from 'react';

import {authService} from '../api/AuthService';
import {accessTokenStorage} from '../stores/AccessTokenStorage';
import {isJwtExpired} from '../utils/jwtUtils';
import {useIsAfk} from './useIsAfk';

interface IToken {
    accesToken?: string;
    refreshToken?: string;
}

interface ITokenUserInfo {
    userToken: IToken;
    isAuthenticated: boolean;
    isAuthLoading: boolean;
}

export const useAuthorization = (): ITokenUserInfo => {
    const [userToken, setUserToken] = useState<IToken>({
        accesToken: undefined,
        refreshToken: undefined,
    });
    const [isAuthenticated, setisAuthenticated] = useState<boolean>(false);

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

    const localRefresh = localStorage.getItem('refreshToken') || undefined;
    const isAuthLoading = !!localRefresh && !isJwtExpired(localRefresh, Date.now()) && !isAuthenticated;

    return {
        userToken,
        isAuthenticated,
        isAuthLoading,
    };
};
