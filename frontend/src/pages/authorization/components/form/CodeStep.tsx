import React, {useRef, useEffect} from 'react';
import styled from 'styled-components';

import {CodeInput} from '../CodeInput/CodeInput';
import {ResendCode} from '../ResendCode';

interface IProps {
    email: string;
    changeEmail: () => void;
    setCode: (code: string) => void;
    sendCode: () => void;
}

export const CodeStep: React.FC<IProps> = ({email, setCode, changeEmail, sendCode}) => {
    const inputRef = useRef<HTMLInputElement>(null);

    useEffect(() => {
        if (inputRef.current) {
            inputRef.current.focus();
        }
    }, [inputRef]);

    return (
        <React.Fragment>
            <ResendCode sendCode={sendCode} />
            <div>Мы отправили вам на почту {email} шестизначный код</div>
            <StyledCodeInput ref={inputRef} length={6} onChange={setCode} />
            <button type='button' onClick={changeEmail}>
                Изменить email
            </button>
            <button>click</button>
        </React.Fragment>
    );
};

const StyledCodeInput = styled(CodeInput)`
    & > div {
        margin-bottom: 30px;
    }
`;
