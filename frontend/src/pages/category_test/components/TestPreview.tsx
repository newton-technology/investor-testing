import React from 'react';
import styled from 'styled-components';

import {Button} from '../../../components/Button';
import {Icon} from '../../../components/Icon';

interface IProps {
    title: string;
    isTestVisible: boolean;
    goToTest: () => void;
}

const recommendations: string[] = [
    '7 вопросов, которые покажут насколько вы готовы к сложным инвестиционным сделкам',
    'Чтобы пройти тест необходимо ответить на все вопросы',
    'Пересдавать тест можно любое количество раз',
    'Результат будет зависеть только от ваших знаний, решимости и усердия!',
];

export const TestPreview: React.FC<IProps> = (props) => {
    const {title, goToTest, isTestVisible} = props;

    const handleClick = () => {
        goToTest();
    };

    return (
        <Container>
            <Title>
                <strong>Тема:</strong> {title}
            </Title>
            <Subtitle>Что нужно знать перед началом теста</Subtitle>
            <List>
                {recommendations.map((recommendation: string, i: number) => {
                    return (
                        <ListItem key={i}>
                            <IconContainer name='check_circle' />
                            {recommendation}
                        </ListItem>
                    );
                })}
            </List>
            {!isTestVisible && (
                <ButtonContainer>
                    <Button onClick={handleClick}>Поехали</Button>
                </ButtonContainer>
            )}
        </Container>
    );
};

const Container = styled.div`
    background-color: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 10px;
    padding: 32px;
`;

const Title = styled.div`
    font-size: 24px;

    strong {
        font-weight: 700;
    }
`;

const Subtitle = styled.div`
    margin-bottom: 40px;
    font-weight: 600;
    font-size: 24px;
    margin: 32px 0 24px;
`;

const List = styled.ul`
    list-style: none;
`;

const ListItem = styled.li`
    display: flex;

    & + & {
        margin-top: 16px;
    }
`;

const IconContainer = styled(Icon)`
    margin-right: 16px;
    color: ${({theme}) => theme.palette.primary};
`;

const ButtonContainer = styled.div`
    text-align: center;
    margin-top: 64px;
`;
