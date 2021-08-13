import React from 'react';
import styled from 'styled-components';
import {AnswerControl, IAnswerControl} from './AnswerControl';

export interface IQuestion {
    id: number;
    question: string;
    answersCountToChooseMin: number;
    answersCountToChooseMax: number;
    answers: IAnswerControl[];
}

interface IProps {
    title: string;
    answers: IAnswerControl[];
    index: number;
    questionsCount: number;
    isMultipleAnswers: boolean;
    getIsChecked: (id: number) => boolean;
    changeValue: (id: number) => void;
}

export const QuestionCard: React.FC<IProps> = (props) => {
    const {title, answers, getIsChecked, changeValue, questionsCount, index, isMultipleAnswers} = props;

    return (
        <Container>
            <QuestionNumber>{`${index}/${questionsCount}`}</QuestionNumber>
            <Title>{title}</Title>
            {isMultipleAnswers && <Subtitle>{'(возможно несколько вариантов ответа)'}</Subtitle>}
            <Answers>
                {answers.map((answer) => {
                    return (
                        <AnswerControl
                            key={answer.id}
                            {...answer}
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
    background-color: #fff;
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

const Subtitle = styled.div`
    margin-top: 8px;
`;

const Answers = styled.div`
    margin-top: 32px;
`;
