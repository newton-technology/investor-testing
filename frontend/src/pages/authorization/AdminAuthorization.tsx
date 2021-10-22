import React, {ChangeEvent, FormEvent, useEffect, useState} from 'react';
import styled from 'styled-components';

import {authService} from '../../api/AuthService';
import {ReactComponent as AuthPageBackground} from '../../assets/svg/authPageBackground.svg';
import {Button} from '../../components/Button';
import {Icon} from '../../components/Icon';
import {FormHeader} from './components/FormHeader';
import {emailValidate} from './components/utils';

type IForm = {username: string; password: string};
type isErrorType = {username: boolean; password: boolean};

export const AdminAuthorization: React.FC = () => {
    const [form, setForm] = useState<IForm>({username: '', password: ''});
    const [isError, setIsError] = useState<isErrorType>({username: false, password: false});

    const onSubmit = (e: FormEvent) => {
        e.preventDefault();
        const {username, password} = form;
        const requestData = {username, password, scope: 'admin', grant_type: 'password'};
        authService.sendCode(requestData).catch(() => {
            setIsError({username: true, password: true});
        });
    };

    const onChange = (e: ChangeEvent<HTMLInputElement>) => {
        if ((isError.password && isError.username) || isError.password) setIsError({username: false, password: false});
        setForm((prev) => ({...prev, [e.target.name]: e.target.value}));
    };

    useEffect(() => {
        setIsError((prev) => ({...prev, username: !emailValidate(form.username)}));
    }, [form.username]);

    return (
        <Container>
            <AuthPageBackground style={{position: 'fixed'}} />
            <FormContainer>
                <Form onSubmit={onSubmit}>
                    <FormHeader />
                    <InputContainer>
                        {!form.username.length && <StyledIcon name='email' size={16} />}
                        <Input
                            type='email'
                            placeholder='        Email'
                            value={form.username}
                            name='username'
                            isError={isError.username && !!form.username.length}
                            onChange={onChange}
                        />
                    </InputContainer>
                    <InputContainer>
                        {!form.password.length && <StyledIcon name='email' size={16} />}
                        <Input
                            type='password'
                            placeholder='        Пароль'
                            value={form.password}
                            name='password'
                            isError={isError.password && !!form.password.length}
                            onChange={onChange}
                        />
                    </InputContainer>
                    <Button disabled={isError.username || isError.password} type='submit'>
                        Продолжить
                    </Button>
                </Form>
            </FormContainer>
        </Container>
    );
};

const Container = styled.div`
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
    padding: 0 32px 32px 32px;
    width: 100%;

    ${({theme}) => theme.breakpoint('md')`
        padding: 0 32px 32px 32px;
    `}
`;

const FormContainer = styled.div`
    align-items: center;
    background: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 10px;
    box-sizing: border-box;
    max-width: 457px;
    width: 100%;
    z-index: 2;
`;

const InputContainer = styled.div`
    color: #929bad;
    position: relative;
    width: 100%;
`;

const Input = styled.input<{isError: boolean}>`
    border: 1px solid ${({isError, theme}) => (isError ? theme.palette.error : '#c4c8db')};
    border-radius: 4px;
    box-sizing: border-box;
    color: ${({isError, theme}) => (isError ? theme.palette.error : theme.palette.regular)};
    margin-bottom: 16px;
    outline: none;
    padding: 16px;
    padding-left: 32px;
    width: 100%;
`;

const StyledIcon = styled(Icon)`
    left: 32px;
    position: absolute;
    top: 20px;
`;
