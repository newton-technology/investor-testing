import React from 'react';
import styled from 'styled-components';

import {Icon} from '../../../components/Icon';
import {useCountdown} from '../../../hooks/useCountdown';

interface IProps {
    sendCode: () => void;
}

export const ResendCode: React.FC<IProps> = ({sendCode}) => {
    const {countdown, formatedCountdown, restart} = useCountdown();

    const handleSendCode = () => {
        sendCode();
        restart();
    };

    if (countdown <= 0) {
        return (
            <SendCode type='button' onClick={handleSendCode}>
                <StyledIcon name='refresh' />
                Отправить код
            </SendCode>
        );
    }

    return (
        <Wrapper>
            <StyledIcon name='clock' />
            Выслать код повторно через {formatedCountdown}
        </Wrapper>
    );
};

const StyledIcon = styled(Icon)`
    padding-right: 8px;
`;

const Wrapper = styled.div`
    align-items: center;
    color: #3a3463;
    display: flex;
    font-size: 17px;
    font-weight: 500;
    justify-content: center;
    line-height: 130%;
    padding-bottom: 24px;
`;

const SendCode = styled.button`
    align-items: center;
    color: #2f6feb;
    display: flex;
    font-size: 17px;
    font-style: normal;
    font-weight: normal;
    justify-content: center;
    line-height: 130%;
    padding-bottom: 24px;
`;
