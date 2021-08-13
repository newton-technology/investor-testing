import React from 'react';
import styled from 'styled-components';
import {Button} from '../../../components/Button';
import {Icon} from '../../../components/Icon';

interface IProps {
    title: string;
    subtitle: string;
}

const recommendations: string[] = [
    '7 вопросов, которые покажут насколько вы готовы к сложным инвестиционным сделкам',
    'Чтобы пройти тест необходимо ответить на все вопросы',
    'Пересдавать тест можно любое количество раз',
    'Результат будет зависить только от ваших знаний, решимости и усердия!',
];

export const TestPreview: React.FC<IProps> = (props) => {
    const {title, subtitle} = props;

    const handleClick = () => {
        console.log('kk');
    };

    return (
        <Container>
            <Title>{title}</Title>
            <Subtitle>{subtitle}</Subtitle>
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
            <ButtonContainer>
                <Button onClick={handleClick}>Поехали</Button>
            </ButtonContainer>
        </Container>
    );
};

const Container = styled.div`
    background-color: #fff;
    border-radius: 10px;
    padding: 32px;
`;

const Title = styled.div``;

const Subtitle = styled.div`
    margin-bottom: 40px;
    font-weight: 600;
    font-size: 24px;
`;

const List = styled.ul`
    list-style: none;
    margin: 24px 0 64px;
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
`;
