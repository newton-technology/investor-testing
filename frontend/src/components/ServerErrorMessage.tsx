import React, {ReactElement} from 'react';
import styled from 'styled-components';

import {Button} from './Button';
import {breakpoint} from '../theme/breakpont';

interface IProps {
    title?: string;
    subtitle?: string;
    children?: ReactElement;
}

export const ServerErrorMessage: React.FC<IProps> = (props) => {
    const {title, subtitle, children} = props;

    const handleClick = () => {
        document.location.reload();
    };

    return (
        <Container>
            <Title>{title || 'Ошибка'}</Title>
            <Subtitle>
                {subtitle ||
                    'На сервере произошла непредвиденная ошибка. Пожалуйста, подождите. Она вскоре будет исправлена.'}
            </Subtitle>
            {children || <Button onClick={handleClick}>Повторить попытку</Button>}
        </Container>
    );
};

const Container = styled.div`
    text-align: center;
    padding: 24px;
    background-color: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 10px;
    max-width: 500px;
    margin: 0 auto;

    ${breakpoint('md')`
        padding: 32px;
    `}
`;

const Title = styled.div`
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 8px;

    ${breakpoint('md')`
        font-size: 32px;
    `}
`;

const Subtitle = styled.div`
    margin-bottom: 32px;

    ${breakpoint('md')`
        font-size: 20px;
    `}
`;
