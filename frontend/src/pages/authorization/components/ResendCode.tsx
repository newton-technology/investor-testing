import React from 'react';
import styled from 'styled-components';

import {Icon} from '../../../components/Icon';
import {useCountdown} from '../../../hooks/useCountdown';

interface IProps {
    sendCode: () => void;
}

export const ResendCode: React.FC<IProps> = ({sendCode}) => {
    const {countdown, formatedCountdown, restart} = useCountdown();

    const handleResendCode = () => {
        sendCode();
        restart();
    };

    if (countdown <= 0) {
        return (
            <SendCode type='button' onClick={handleResendCode}>
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
    color: ${({theme}) => theme.palette.regular};
    display: flex;
    font-weight: 500;
    justify-content: center;
    padding-bottom: 24px;
`;

const SendCode = styled.button`
    align-items: center;
    color: ${({theme}) => theme.palette.secondary};
    display: flex;
    font-size: 17px;
    justify-content: center;
    padding-bottom: 24px;
`;
