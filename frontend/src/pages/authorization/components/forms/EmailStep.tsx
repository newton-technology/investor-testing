import React from 'react';
import styled from 'styled-components';

import {Button} from '../../../../components/Button';
import {Icon} from '../../../../components/Icon';
import customize from '../../../../customize.json';

const {eula} = customize.content || {};

interface IProps {
    email: string;
    isError: boolean;
    isServerError: boolean;
    isAuthLoading: boolean;
    setEmail: (email: string) => void;
}
export const EmailStep: React.FC<IProps> = ({email, isError, isAuthLoading, isServerError, setEmail}) => {
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
            <ButtonContainer>
                <StyledButton disabled={isError || isAuthLoading || email.length < 1} isLoading={isAuthLoading}>
                    Продолжить
                </StyledButton>
                {isServerError && <ErrorMessage>Ошибка сервера, повторите позже.</ErrorMessage>}
                {isError && !!email.length && <ErrorMessage>Неправильно введен email.</ErrorMessage>}
            </ButtonContainer>
            {eula && <EULADescription dangerouslySetInnerHTML={{__html: eula}} />}
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

const ErrorMessage = styled.div`
    color: ${({theme}) => theme.palette.error};
    position: absolute;
    text-align: center;
    top: 70px;
`;

const StyledIcon = styled(Icon)`
    left: 32px;
    position: absolute;
    top: 20px;
`;

const EULADescription = styled.span`
    color: #a9a9a9;
    font-size: 14px;
    line-height: 130%;
    padding-bottom: 32px;
    padding-top: 54px;

    a {
        text-decoration: underline;
    }
`;

const StyledButton = styled(Button)`
    width: 100%;
`;

const ButtonContainer = styled.div`
    display: flex;
    justify-content: center;
    position: relative;
    width: 100%;
`;
