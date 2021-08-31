import React from 'react';
import styled from 'styled-components';

import {Button} from '../../../../components/Button';
import {Icon} from '../../../../components/Icon';

interface IProps {
    email: string;
    isError: boolean;
    isServerError: boolean;
    setEmail: (email: string) => void;
}
export const EmailStep: React.FC<IProps> = ({email, isError, isServerError, setEmail}) => {
    return (
        <React.Fragment>
            <Description>
                Мы поможем узнать, насколько хорошо вы разбираетесь в инструментах инвестирования, какие сложные сделки
                можете заключать уже сейчас, и какой вид сделок принесет вам максимальную прибыль в будущем
            </Description>
            <InputContainer>
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
            </InputContainer>
            <Button disabled={isError}>Продолжить</Button>
            {isServerError && <ErrorMessage>Ошибка сервера, повторите позже.</ErrorMessage>}
            {isError && !!email.length && <ErrorMessage>Неправильно введен email.</ErrorMessage>}
            <EULADescription>
                Нажимая кнопку Продолжить, вы соглашаетесь с условиями <u>пользовательского соглашения</u> и даете{' '}
                <u>согласие</u> на обработку ваших персональных данных
            </EULADescription>
        </React.Fragment>
    );
};

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
const Description = styled.span`
    padding-bottom: 24px;
`;

const ErrorMessage = styled.span`
    color: ${({theme}) => theme.palette.error};
    padding-top: 16px;
    text-align: center;
`;
const StyledIcon = styled(Icon)`
    left: 32px;
    position: absolute;
    top: 20px;
`;

const EULADescription = styled.span`
    color: #a9a9a9;
    font-size: 12px;
    line-height: 130%;
    padding-bottom: 32px;
    padding-top: 16px;
`;
