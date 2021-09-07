import React, {useEffect, useRef} from 'react';
import styled from 'styled-components';

import {AnswerControl, IAnswerControl} from './AnswerControl';
import {HintIcon} from './HintIcon';

export interface IQuestion {
    id: number;
    question: string;
    answersCountToChooseMin: number;
    answersCountToChooseMax: number;
    answers: IAnswerControl[];
    hint?: string;
}

interface IProps {
    title: string;
    id: number;
    answers: IAnswerControl[];
    index: number;
    questionsCount: number;
    isMultipleAnswers: boolean;
    hint?: string;
    isError: boolean;
    getIsChecked: (questionId: number, answerId: number) => boolean;
    changeValue: (questionId: number, answerId: number, isMultipleAnswers: boolean) => void;
}

export const QuestionCard: React.FC<IProps> = (props) => {
    const {title, id, answers, getIsChecked, changeValue, questionsCount, index, isMultipleAnswers, isError, hint} =
        props;
    const ref = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (isError && ref.current) {
            ref.current.scrollIntoView({behavior: 'smooth'});
        }
    }, [isError]);

    return (
        <Container ref={ref}>
            <QuestionNumber>{`${index}/${questionsCount}`}</QuestionNumber>
            <Title>
                {title} {hint && <HintIcon>{hint}</HintIcon>}
            </Title>
            {isMultipleAnswers && <Subtitle>{'(возможно несколько вариантов ответа)'}</Subtitle>}
            {isError && <ErrorMessage>Пожалуйста, выберите вариант ответа</ErrorMessage>}
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
    padding: 32px 24px;
    font-size: 17px;
    margin-top: 24px;

    ${({theme}) => theme.breakpoint('md')`
        padding: 32px;
    `}
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
    font-size: 14px;

    ${({theme}) => theme.breakpoint('md')`
        font-size: inherit;
    `}
`;

const ErrorMessage = styled.div`
    color: ${({theme}) => theme.palette.error};
    margin-top: 8px;
    font-size: 14px;

    ${({theme}) => theme.breakpoint('md')`
        font-size: inherit;
    `}
`;

const Answers = styled.div`
    margin-top: 32px;
`;
