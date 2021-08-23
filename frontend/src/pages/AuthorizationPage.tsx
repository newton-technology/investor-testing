import React, {useState, useRef, useEffect} from 'react';
import styled from 'styled-components';

import {authService} from '../api/AuthService';
import {Button} from '../components/Button';
import {CodeInput} from '../components/CodeInput/CodeInput';
import {accessTokenStorage} from '../stores/AccessTokenStorage';

const steps = {
    email: {
        title: 'Войдите, чтобы начать тестирование',
    },
    code: {
        title: 'Введите код',
    },
};

export const AuthorizationPage: React.FC = () => {
    const [email, setEmail] = useState<string>('');
    const [code, setCode] = useState<string>('');
    const inputRef = useRef<HTMLInputElement>(null);
    const [step, setStep] = useState<'email' | 'code'>('email');

    useEffect(() => {
        if (inputRef.current) {
            inputRef.current.focus();
        }
    }, [inputRef]);

    const sendCode = () => {
        authService.sendCode({
            email,
            grant_type: 'code',
        });
        setStep('code');
    };

    const login = () => {
        if (accessTokenStorage?.accessToken) {
            authService.login({
                email,
                code,
                access_token: accessTokenStorage?.accessToken,
                grant_type: 'code',
            });
        }
    };

    return (
        <Wrapper>
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
                    <Title>{steps[step].title}</Title>
                    {step === 'email' ? (
                        <Input
                            type='email'
                            value={email}
                            onChange={(e) => {
                                setEmail(e.target.value);
                            }}
                        />
                    ) : (
                        <StyledCodeInput ref={inputRef} length={6} onChange={setCode} />
                    )}
                    <Button>Продолжить</Button>
                </Form>
            </FormContainer>
        </Wrapper>
    );
};

const Wrapper = styled.div`
    align-items: center;
    background: #587cfc;
    display: flex;
    height: 100vh;
    justify-content: center;
    width: 100%;
`;

const Form = styled.form`
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    height: 100%;
    justify-content: center;
    padding: 0 32px;
    width: 100%;
`;

const FormContainer = styled.div`
    align-items: center;
    background: #ffffff;
    border-radius: 10px;
    box-sizing: border-box;
    height: 378px;
    width: 444px;
`;

const Input = styled.input`
    padding: 16px;
`;

const Title = styled.h1`
    text-align: center;
`;

const StyledCodeInput = styled(CodeInput)`
    & > div {
        margin-bottom: 30px;
    }
`;
