import React, {useRef, useEffect} from 'react';
import styled from 'styled-components';

import {Icon} from '../../../../components/Icon';
import {emailEllipsisFormat} from '../../../../utils/emailEllipsisFormat';
import {CodeInput} from '../CodeInput/CodeInput';
import {ResendCode} from '../ResendCode';

interface IProps {
    email: string;
    isWrongCode: boolean;
    changeEmail: () => void;
    setCode: (code: string) => void;
    sendCode: () => void;
    login: () => void;
}

export const CodeStep: React.FC<IProps> = ({email, isWrongCode, setCode, changeEmail, sendCode, login}) => {
    const inputRef = useRef<HTMLInputElement>(null);

    useEffect(() => {
        if (inputRef.current) {
            inputRef.current.focus();
        }
    }, [inputRef]);

    return (
        <React.Fragment>
            <Description>
                Мы отправили вам на почту <br /> <Email>{emailEllipsisFormat(email)}</Email> шестизначный код
            </Description>
            <StyledCodeInput ref={inputRef} length={6} onChange={setCode} onComplete={login} error={isWrongCode} />
            <ResendCode sendCode={sendCode} />
            {isWrongCode && <ErrorMessage>Неверный код </ErrorMessage>}
            <ChangeEmailButton type='button' onClick={changeEmail}>
                <Icon name='chevron' />
                Изменить email
            </ChangeEmailButton>
        </React.Fragment>
    );
};

const StyledCodeInput = styled(CodeInput)`
    & > div {
        margin-bottom: 30px;
    }
`;

const Description = styled.span`
    color: #3a3463;
    font-family: 'IBM Plex Sans', sans-serif;
    font-size: 17px;
    font-style: normal;
    font-weight: normal;
    line-height: 130%;
    padding-bottom: 35px;
`;

const Email = styled.span`
    color: #0daaec;
    overflow-wrap: anywhere;
`;

const ChangeEmailButton = styled.button`
    align-items: center;
    color: #2f6feb;
    display: flex;
    font-size: 17px;
    font-weight: 500;
    justify-content: center;
    line-height: 130%;
    padding-bottom: 32px;

    & > span {
        margin-right: 8px;
    }
`;
const ErrorMessage = styled.div`
    color: #de2b37;
    display: flex;
    font-size: 17px;
    justify-content: center;
    line-height: 130%;
    padding-bottom: 32px;
    padding-top: 8px;
`;
