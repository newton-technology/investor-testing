import React, {useState, useRef, useEffect} from 'react';
import styled from 'styled-components';

import {authService} from '../../api/AuthService';
import {ReactComponent as AuthPageBackground} from '../../assets/svg/authPageBackground.svg';
import {Button} from '../../components/Button';
import {Icon} from '../../components/Icon';
import {accessTokenStorage} from '../../stores/AccessTokenStorage';
import {CodeInput} from './components/CodeInput/CodeInput';
import {ResendCode} from './components/ResendCode';

const steps = {
    email: {
        title: 'Войдите, чтобы начать тестирование',
    },
    code: {
        title: 'Введите код',
    },
};

const emailRegex =
    // eslint-disable-next-line max-len
    /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

const emailValidate = (email: string): boolean => emailRegex.test(email);

export const AuthorizationPage: React.FC = () => {
    const [email, setEmail] = useState<string>('');
    const [code, setCode] = useState<string>('');
    const [isError, setIsError] = useState<boolean>(false);
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

    useEffect(() => {
        setIsError(!emailValidate(email));
    }, [email]);

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
                    <Title>{steps[step].title}</Title>
                    <ResendCode />
                    {step === 'email' ? (
                        <React.Fragment>
                            <Description>
                                Мы поможем узнать, насколько хорошо вы разбираетесь в инструментах инвестирования, какие
                                сложные сделки можете заключать уже сейчас, и какой вид сделок принесет вам максимальную
                                прибыль в будущем
                            </Description>
                            <InputWrapper>
                                {!email.length && <StyledIcon name='email' size={16} />}
                                <Input
                                    type='email'
                                    placeholder='        Email'
                                    value={email}
                                    isError={isError && !!email.length}
                                    onChange={(e) => {
                                        setEmail(e.target.value);
                                    }}
                                />
                            </InputWrapper>
                        </React.Fragment>
                    ) : (
                        <StyledCodeInput ref={inputRef} length={6} onChange={setCode} />
                    )}
                    <Button disabled={isError}>Продолжить</Button>
                    {isError && !!email.length && <ErrorMessage>Неправильно введен email.</ErrorMessage>}
                </Form>
            </FormContainer>
        </Wrapper>
    );
};

const Wrapper = styled.div`
    align-items: center;
    /* background: #587cfc;  */
    background: linear-gradient(139.02deg, #65dfe7 -19.37%, #587cfc 116.76%);
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
    height: 573px;
    width: 444px;
    z-index: 2;
`;

const InputWrapper = styled.div`
    position: relative;
    width: 100%;
`;

const Input = styled.input<{isError: boolean}>`
    border: 1px solid ${({isError}) => (isError ? '#e30b17' : '#c4c8db')};
    border-radius: 4px;
    box-sizing: border-box;
    color: ${({isError}) => (isError ? '#e30b17' : 'black')};
    font-family: 'IBM Plex Sans', sans-serif;
    font-size: 17px;
    font-style: normal;
    font-weight: normal;
    line-height: 130%;
    margin-bottom: 16px;
    outline: none;
    padding: 16px;
    padding-left: 32px;
    width: 100%;

    &::placeholder {
        /* padding-left: 64px; */
        text-indent: 50px;
    }
`;

const Title = styled.h1`
    color: #3a3463;
    font-family: 'IBM Plex Sans', sans-serif;
    font-size: 32px;
    font-style: normal;
    font-weight: bold;
    line-height: 130%;
`;

const Description = styled.span`
    color: #3a3463;
    font-family: 'IBM Plex Sans', sans-serif;
    font-size: 17px;
    font-style: normal;
    font-weight: normal;
    line-height: 130%;
    padding-bottom: 24px;
`;

const ErrorMessage = styled.span`
    color: #de2b37;
    font-family: 'IBM Plex Sans', sans-serif;
    font-size: 17px;
    font-style: normal;
    font-weight: normal;
    line-height: 130%;
    text-align: center;

    /* visibility hidden
     */
`;

const StyledCodeInput = styled(CodeInput)`
    & > div {
        margin-bottom: 30px;
    }
`;

const StyledIcon = styled(Icon)`
    left: 32px;
    position: absolute;
    top: 20px;
`;
