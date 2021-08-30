import React, {useState, useEffect} from 'react';
import styled from 'styled-components';

import {authService} from '../../api/AuthService';
import {ReactComponent as AuthPageBackground} from '../../assets/svg/authPageBackground.svg';
import {accessTokenStorage} from '../../stores/AccessTokenStorage';
import {FormHeader} from './components/FormHeader';
import {CodeStep} from './components/forms/CodeStep';
import {EmailStep} from './components/forms/EmailStep';

const steps = {
    email: {
        title: 'Войдите, чтобы начать тестирование',
    },
    code: {
        title: 'Введите код из письма',
    },
};

const emailRegex =
    // eslint-disable-next-line max-len
    /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

const emailValidate = (email: string): boolean => emailRegex.test(email);

export const Authorization: React.FC = () => {
    const [email, setEmail] = useState<string>('');
    const [code, setCode] = useState<string>('');
    const [isError, setIsError] = useState<boolean>(false);
    const [isServerError, setIsServerError] = useState<boolean>(false);
    const [isWrongCode, setIsWrongCode] = useState<boolean>(false);
    const [step, setStep] = useState<'email' | 'code'>('email');

    const sendCode = () => {
        authService
            .sendCode({
                email,
                grant_type: 'code',
            })
            .then(() => {
                setStep('code');
                setIsServerError(false);
            })
            .catch(() => setIsServerError(true));
    };

    const login = () => {
        if (accessTokenStorage?.accessToken) {
            authService
                .login({
                    email,
                    code,
                    access_token: accessTokenStorage?.accessToken,
                    grant_type: 'code',
                })
                .catch(() => setIsWrongCode(true));
        }
    };

    const changeEmail = () => {
        setEmail('');
        setCode('');
        setStep('email');
    };

    useEffect(() => {
        setIsError(!emailValidate(email));
    }, [email]);

    useEffect(() => {
        if (code.length === 0) {
            setIsWrongCode(false);
        }
    }, [code]);

    return (
        <Wrapper>
            <AuthPageBackground style={{position: 'fixed'}} />
            <FormContainer>
                <Form
                    onSubmit={(e) => {
                        e.preventDefault();
                        if (step === 'email') {
                            sendCode();
                        } else {
                            login();
                        }
                    }}>
                    {step === 'email' && <FormHeader />}
                    <Title>{steps[step].title}</Title>
                    {step === 'email' ? (
                        <EmailStep email={email} isError={isError} isServerError={isServerError} setEmail={setEmail} />
                    ) : (
                        <CodeStep
                            email={email}
                            changeEmail={changeEmail}
                            setCode={setCode}
                            sendCode={sendCode}
                            login={login}
                            isWrongCode={isWrongCode}
                        />
                    )}
                </Form>
            </FormContainer>
        </Wrapper>
    );
};

const Wrapper = styled.div`
    align-items: center;
    background: ${({theme}) => theme.palette.bg.authorization};
    display: flex;
    height: 100vh;
    justify-content: center;
    padding: 0 32px;
    width: 100%;
`;

const Form = styled.form`
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 0 32px;
    width: 100%;

    ${({theme}) => theme.breakepoint('xs')`
        padding: 0 24px;
    `}
`;

const FormContainer = styled.div`
    align-items: center;
    background: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 10px;
    box-sizing: border-box;
    max-width: 457px;
    z-index: 2;
`;

const Title = styled.h1`
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 16px;
    margin-top: 15px;

    ${({theme}) => theme.breakepoint('xs')`
        font-size: 28px;
    `}
`;
