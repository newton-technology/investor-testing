import React, {useRef, useEffect} from 'react';
import styled from 'styled-components';

import {Icon} from '../../../../components/Icon';
import {emailEllipsisFormat} from '../../../../utils/emailEllipsisFormat';
import {CodeInput} from '../CodeInput/CodeInput';
import {ResendCode} from '../ResendCode';

interface IProps {
    email: string;
    changeEmail: () => void;
    setCode: (code: string) => void;
    sendCode: () => void;
    login: () => void;
}

export const CodeStep: React.FC<IProps> = ({email, setCode, changeEmail, sendCode, login}) => {
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
            <StyledCodeInput ref={inputRef} length={6} onChange={setCode} onComplete={login} />
            <ResendCode sendCode={sendCode} />
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
    font-weight: 500;
    font-size: 17px;
    line-height: 130%;
    color: #2f6feb;

    display: flex;
    justify-content: center;
    align-items: center;
    padding-bottom: 32px;

    & > span {
        margin-right: 8px;
    }
`;
