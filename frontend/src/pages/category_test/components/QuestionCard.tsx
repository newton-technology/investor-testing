import React from 'react';
import styled from 'styled-components';

import {AnswerControl, IAnswerControl} from './AnswerControl';
import {Icon} from '../../../components/Icon';

export interface IQuestion {
    id: number;
    question: string;
    answersCountToChooseMin: number;
    answersCountToChooseMax: number;
    answers: IAnswerControl[];
}

interface IProps {
    title: string;
    id: number;
    answers: IAnswerControl[];
    index: number;
    questionsCount: number;
    isMultipleAnswers: boolean;
    getIsChecked: (questionId: number, answerId: number) => boolean;
    changeValue: (questionId: number, answerId: number, isMultipleAnswers: boolean) => void;
}

export const QuestionCard: React.FC<IProps> = (props) => {
    const {title, id, answers, getIsChecked, changeValue, questionsCount, index, isMultipleAnswers} = props;

    return (
        <Container>
            <QuestionNumber>{`${index}/${questionsCount}`}</QuestionNumber>
            <Title>
                {title}{' '}
                <IconContainer>
                    <Icon name='info' />
                    <Tooltip>{title}</Tooltip>
                </IconContainer>
            </Title>
            {isMultipleAnswers && <Subtitle>{'(возможно несколько вариантов ответа)'}</Subtitle>}
            <Answers>
                {answers.map((answer) => {
                    return (
                        <AnswerControl
                            key={answer.id}
                            {...answer}
                            questionId={id}
                            getIsChecked={getIsChecked}
                            changeValue={changeValue}
                            isMultipleAnswers={isMultipleAnswers}
                        />
                    );
                })}
            </Answers>
        </Container>
    );
};

const Container = styled.div`
    border-radius: 10px;
    background-color: ${({theme}) => theme.palette.bg.secondary};
    padding: 32px;
    font-size: 17px;
    margin-top: 24px;
`;

const QuestionNumber = styled.div`
    color: ${({theme}) => theme.palette.primary};
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 16px;
    text-align: center;
`;

const Title = styled.div`
    font-size: 20px;
    font-weight: 600;
`;

const Tooltip = styled.div`
    display: none;
    font-weight: 400;
    position: absolute;
    font-size: 16px;
    padding: 16px;
    background-color: ${({theme}) => theme.palette.regular};
    color: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 10px;
    top: calc(100% + 12px);
    left: 50%;
    transform: translateX(-50%);

    &:before {
        content: '';
        width: 0;
        display: block;
        height: 0;
        border: 10px solid transparent;
        border-bottom-color: ${({theme}) => theme.palette.regular};
        position: absolute;
        top: -20px;
        left: 50%;
        transform: translateX(-50%);
    }
`;

const IconContainer = styled.span`
    position: relative;
    cursor: pointer;
    color: ${({theme}) => theme.palette.secondary};

    &:hover {
        color: ${({theme}) => theme.palette.regular};

        ${Tooltip} {
            display: block;
        }
    }
`;

const Subtitle = styled.div`
    margin-top: 8px;
`;

const Answers = styled.div`
    margin-top: 32px;
`;
