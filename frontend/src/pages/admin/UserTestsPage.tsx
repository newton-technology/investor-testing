import React from 'react';
import styled from 'styled-components';

import {dateFormatter} from '../../utils/tableUtils';

export const UserTestsPage: React.FC = () => {
    return (
        <Container>
            <Title>Klivjv89zzQыS7@gmail.com</Title>
            <Paper>
                <PaperContent isFlex>
                    <Label>Дата и время прохождения:</Label>
                    <Text>{dateFormatter(1627894786, 'D MMMM YYYY; H:m (МСК)')}</Text>
                </PaperContent>
                <PaperContent isFlex>
                    <Label>Результат:</Label>
                    <Text isBold>Тест не пройден</Text>
                </PaperContent>
                <PaperContent>
                    <Label>Название теста:</Label>
                    <Text>
                        Сделки по приобретению облигаций иностранных эмитентов, исполнение обязательств по которым
                        обеспечивается или осуществляется за счет юридического лица, созданного в соответствии с
                        законодательством Российской Федерации, не имеющего кредитный рейтинг иликредитный рейтинг
                        которого ниже уровня, установленного Советом директоров Банка России.
                    </Text>
                </PaperContent>
            </Paper>
            <SubTitle>Список вопросов и ответы на них</SubTitle>
            <Paper></Paper>
        </Container>
    );
};

const Container = styled.div`
    margin: 0 auto;
    max-width: 762px;
`;

const Title = styled.div`
    color: ${({theme}) => theme.palette.regular};
    font-size: 36px;
    font-weight: 500;
    line-height: 130%;
    margin-bottom: 32px;
`;

const Paper = styled.div`
    background: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 10px;
    margin-bottom: 48px;
    padding: 32px;
`;

const PaperContent = styled.div<{isFlex?: boolean}>`
    align-items: center;
    display: ${({isFlex}) => (isFlex ? 'flex' : 'block')};
    width: 100%;

    &:not(last-child) {
        margin-bottom: 24px;
    }
`;

const Label = styled.div`
    font-size: 17px;
    font-weight: 500;
    line-height: 130%;
    margin-right: 58px;
    width: 229px;
`;

const Text = styled.div<{isBold?: boolean}>`
    color: ${({theme}) => theme.palette.regular};
    font-size: 17px;
    font-weight: ${({isBold}) => (isBold ? 'bold' : 'normal')};
    line-height: 130%;
`;

const SubTitle = styled.div`
    color: ${({theme}) => theme.palette.regular};
    font-size: 28px;
    font-weight: 500;
    line-height: 130%;
    margin-bottom: 24px;
`;
