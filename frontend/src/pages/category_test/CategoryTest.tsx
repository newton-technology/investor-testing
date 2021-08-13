import React, {createContext, useState} from 'react';
import {useParams} from 'react-router-dom';
import styled from 'styled-components';

import {IQuestion, QuestionCard} from './components/QuestionCard';
import {useQuery} from '../../hooks/useQuery';
import {CategoryTestApi} from '../../api/CategoryTestApi';
import {Loader} from '../../components/Loader';
import {Button} from '../../components/Button';
import {TestPreview} from './components/TestPreview';

export interface ITest {
    id: number;
    status: string;
    questions: IQuestion[];
    category: {
        name: string;
        description: string;
    };
}

export const CategoryTest: React.FC = () => {
    const {id} = useParams<{id: string}>();
    const {data, isLoading} = useQuery(() => CategoryTestApi.getTest(id));
    const [values, setValues] = useState(new Set());

    const {questions, category} = data || {};

    const getIsChecked = (answerId: number) => {
        return values.has(answerId);
    };

    const changeValue = (answerId: number) => {
        const newValues = new Set(values);

        if (getIsChecked(answerId)) {
            newValues.delete(answerId);
        } else {
            newValues.add(answerId);
        }

        setValues(newValues);
    };

    const handleSubmit = () => {
        console.log();
    };

    if (isLoading) {
        return <Loader />;
    }

    return (
        <Container>
            <TestPreview title={category.name} subtitle={category.description} />
            {questions && questions.length > 0 && (
                <>
                    <QuestionsList>
                        {questions.map((question: IQuestion, i: number) => {
                            const isMultipleAnswers =
                                question.answersCountToChooseMin !== question.answersCountToChooseMax;

                            return (
                                <QuestionCard
                                    key={question.id}
                                    title={question.question}
                                    answers={question.answers}
                                    getIsChecked={getIsChecked}
                                    changeValue={changeValue}
                                    questionsCount={questions.length}
                                    index={i + 1}
                                    isMultipleAnswers={isMultipleAnswers}
                                />
                            );
                        })}
                    </QuestionsList>
                    <ButtonContainer>
                        <Button onClick={handleSubmit}>Завершить тест</Button>
                    </ButtonContainer>
                </>
            )}
        </Container>
    );
};

const Container = styled.div`
    max-width: 566px;
    margin: 0 auto;
`;

const QuestionsList = styled.div`
    margin-bottom: 64px;
`;

const ButtonContainer = styled.div`
    text-align: center;
`;
