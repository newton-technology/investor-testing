import React, {useEffect} from 'react';

import AuthService from '../api/AuthService';

export const useAuthorization = (): undefined => {
    const [token, setToken] = React.useState<undefined>();

    useEffect(() => {
        AuthService.Init();
    }, []);

    return token;
};
