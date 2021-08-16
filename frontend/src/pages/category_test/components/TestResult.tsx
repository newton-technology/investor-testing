import React from 'react';
import styled from 'styled-components';

import {Button} from '../../../components/Button';

export const TestResult: React.FC = (props) => {
    return (
        <Container>
            <Panel>
                <Title>Тест не пройден</Title>
                <Subtitle>Наберите максимальный балл для прохождения теста</Subtitle>
                <ButtonsContainer>
                    <Button isPlain>Вернуться на главную</Button>
                    <Button>Попробовать еще раз</Button>
                </ButtonsContainer>
            </Panel>
        </Container>
    );
};

const Container = styled.div`
    display: flex;
    justify-content: center;
    margin-top: 60px;
`;

const Panel = styled.div`
    text-align: center;
    padding: 32px 60px;
    background-color: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 10px;
`;

const Title = styled.div`
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 8px;
`;

const Subtitle = styled.div`
    font-size: 20px;
    margin-bottom: 40px;
`;

const ButtonsContainer = styled.div`
    display: grid;
    grid-gap: 20px;
    grid-template-columns: auto auto;
`;
