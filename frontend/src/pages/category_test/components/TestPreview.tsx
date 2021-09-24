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
    'Всего 7 вопросов: три для самооценки и четыре на знания особенностей финансовых инструментов',
    'Тест пройден, если все ответы на вопросы блока «Знания» правильные',
    'Пересдавать тест можно любое количество раз',
    'Ограничений по времени нет',
    'На почту придёт уведомление о результате тестирования',
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
    padding: 32px 24px;

    ${({theme}) => theme.breakpoint('md')`
        padding: 32px;
    `}
`;

const Title = styled.div`
    font-weight: 500;

    ${({theme}) => theme.breakpoint('md')`
        font-size: 24px;
        font-weight: 400;
    `}

    strong {
        font-weight: 700;
    }
`;

const Subtitle = styled.div`
    font-weight: 500;
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
    padding-top: 3px;
    color: ${({theme}) => theme.palette.primary};
`;

const ButtonContainer = styled.div`
    text-align: center;
    margin-top: 64px;
`;
