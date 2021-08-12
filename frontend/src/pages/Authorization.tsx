import React, {useState} from 'react';

import AuthService from '../api/AuthService';
import AccessTokenStorage from '../stores/AccessTokenStorage';

const Authorization: React.FC = () => {
    const [email, setEmail] = useState<string>('');
    const [code, setCode] = useState<string>('');

    const [step, setStep] = useState<string>('code');

    const sendCode = () => {
        AuthService.sendCode({
            email,
            grant_type: 'code',
        });
        setStep('login');
    };

    const login = () => {
        if (AccessTokenStorage?.accessToken) {
            AuthService.login({
                email,
                code,
                accessToken: AccessTokenStorage?.accessToken,
                grant_type: 'code',
            });
        }
    };

    return (
        <form
            onSubmit={(e) => {
                e.preventDefault();
                if (step === 'code') {
                    sendCode();
                } else {
                    login();
                }
            }}>
            <input
                type='email'
                value={email}
                onChange={(e) => {
                    setEmail(e.target.value);
                }}
            />
            <input
                type='text'
                value={code}
                onChange={(e) => {
                    setCode(e.target.value);
                }}
            />
            <button type='submit'>kek</button>
        </form>
    );
};

export default Authorization;
