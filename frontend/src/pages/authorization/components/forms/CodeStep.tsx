import React, {useState, useRef, useEffect} from 'react';
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
    const [value, setValue] = useState<string>('');

    const resendCode = () => {
        sendCode();
        setValue('');
        if (inputRef.current) {
            inputRef.current.focus();
        }
    };

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
            <StyledCodeInput
                ref={inputRef}
                length={6}
                onChange={setCode}
                onComplete={login}
                error={isWrongCode}
                value={value}
                setValue={setValue}
            />
            <ResendCode sendCode={resendCode} />
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
    padding-bottom: 35px;
    text-align: center;
`;

const Email = styled.span`
    color: ${({theme}) => theme.palette.primary};
    overflow-wrap: anywhere;
`;

const ChangeEmailButton = styled.button`
    align-items: center;
    color: ${({theme}) => theme.palette.secondary};
    display: flex;
    font-weight: 500;
    justify-content: center;

    & > span {
        margin-right: 8px;
    }
`;
const ErrorMessage = styled.div`
    color: ${({theme}) => theme.palette.error};
    display: flex;
    justify-content: center;
    padding-bottom: 32px;
    padding-top: 8px;
`;
