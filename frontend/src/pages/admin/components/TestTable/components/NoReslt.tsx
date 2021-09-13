import React from 'react';
import styled from 'styled-components';

import {Icon} from '../../../../../components/Icon';

const NoReslt: React.FC = () => {
    return (
        <Container>
            <NotFountText>Совпадений не обнаружено</NotFountText>
            <Icon name='not_found' size={250} />
        </Container>
    );
};

export default NoReslt;

const Container = styled.div`
    align-items: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    margin-top: 80px;
`;

const NotFountText = styled.div`
    font-size: 36px;
    font-weight: 500;
    line-height: 130%;
    margin-bottom: 24px;
    text-align: center;
    width: 100%;
`;
