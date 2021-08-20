import React from 'react';
import styled from 'styled-components';

import {Button} from './Button';

interface IProps {
    onClick?: () => void;
}

export const ServerErrorMessage: React.FC<IProps> = ({onClick}) => {
    const handleClick = () => {
        if (onClick) {
            onClick();
        } else {
            document.location.reload();
        }
    };

    return (
        <Container>
            <Title>Ошибка</Title>
            <Subtitle>
                На сервере произошла непредвиденная ошибка. Пожалуйста, подождите. Она вскоре будет исправлена.
            </Subtitle>
            <Button onClick={handleClick}>Повторить попытку</Button>
        </Container>
    );
};

const Container = styled.div`
    text-align: center;
    padding: 32px 30px;
    background-color: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 10px;
    max-width: 500px;
    margin: 0 auto;
`;

const Title = styled.div`
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 8px;
`;

const Subtitle = styled.div`
    font-size: 20px;
    margin-bottom: 32px;
`;
