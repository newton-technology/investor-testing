import React from 'react';
import {useParams} from 'react-router';
import {Link} from 'react-router-dom';
import styled from 'styled-components';

import {Icon} from '../../components/Icon';

import {Status} from '../../api/ManagmentApi';
import {useUserTestById} from '../../hooks/useAdmin';
import {useScrollToTop} from '../../hooks/useScrollToTop';
import {dateFormatter} from '../../utils/tableUtils';

const translateStatus = {
    [Status.PASSED]: 'Тест пройден',
    [Status.FAILED]: 'Тест не пройден',
    [Status.CANCELED]: '',
    [Status.DRAFT]: '',
    [Status.PROCESSING]: '',
};

export const UserTestsPage: React.FC = () => {
    const {id} = useParams<{id: string}>();
    const {data: test} = useUserTestById(id);

    useScrollToTop();

    if (!test) {
        return null;
    }

    const {userEmail, completedAt, category, questions} = test;

    return (
        <Container>
            <BreadcrumpsContainer>
                <Breadcrump to='/'>
                    <Chevron name='chevron_right' />
                    Назад
                </Breadcrump>
                <Breadcrump to={`/test/${id}`}>
                    <Chevron name='chevron_right' />
                    Все тесты пользователя
                </Breadcrump>
            </BreadcrumpsContainer>
            <Title>{userEmail}</Title>
            <Paper>
                <PaperContent isFlex>
                    <Label>Дата и время прохождения:</Label>
                    <Text>{dateFormatter(completedAt, 'D MMMM YYYY; HH:mm (МСК)')}</Text>
                </PaperContent>
                <PaperContent isFlex>
                    <Label>Результат:</Label>
                    <Text isBold>{translateStatus[test.status]}</Text>
                </PaperContent>
                <PaperContent>
                    <Label>Название теста:</Label>
                    <Text>{category.description}</Text>
                </PaperContent>
            </Paper>
            <SubTitle>Список вопросов и ответы на них</SubTitle>
            <Paper>
                {questions.map((item, index) => (
                    <QuestionContaoner key={item.id}>
                        <QuestionTitle>
                            <span>{index + 1}.</span>
                            {item.question}
                        </QuestionTitle>
                        {item.answers.map((answer) => (
                            <AnswerRow key={answer.id}>
                                {answer.selected ? <CheckedIcon /> : <Dot />}
                                <AnswerText isSelected={answer.selected}>{answer.answer}</AnswerText>
                            </AnswerRow>
                        ))}
                    </QuestionContaoner>
                ))}
            </Paper>
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

const BreadcrumpsContainer = styled.div`
    align-items: center;
    display: flex;
    margin-bottom: 40px;
    width: 100%;
`;

const Breadcrump = styled(Link)`
    align-items: center;
    color: #2f6feb;
    display: flex;
    margin-right: 24px;
    font-weight: bold;
    font-size: 18px;
    line-height: 130%;
`;

const Chevron = styled(Icon)`
    margin-right: 8px;
    transform: rotate(180deg);

    path {
        fill: #2f6feb;
    }
`;

const QuestionContaoner = styled.div`
    margin-bottom: 48px;

    :last-child {
        margin-bottom: 0;
    }
`;

const QuestionTitle = styled.div`
    font-size: 17px;
    line-height: 130%;
    margin-bottom: 24px;

    span {
        font-weight: bold;
        margin-right: 4px;
    }
`;

const AnswerRow = styled.div`
    align-items: flex-start;
    display: flex;
    margin-bottom: 16px;

    :last-child {
        margin-bottom: 0;
    }
`;

const Dot = styled.div`
    align-items: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 21px;
    min-width: 21px;

    :before {
        background-color: ${({theme}) => theme.palette.regular};
        border-radius: 50%;
        content: '';
        display: block;
        height: 6px;
        opacity: 0.7;
        width: 6px;
    }
`;

const CheckedIcon = styled(Icon).attrs({name: 'check', size: 21})``;

const AnswerText = styled.div<{isSelected: boolean}>`
    font-size: 17px;
    ${({isSelected}) => (isSelected ? 'font-weight: bold' : '')};
    line-height: 130%;
    margin-left: 45px;
`;
