import React, {useRef, useState, useEffect} from 'react';
import {useParams, Prompt} from 'react-router-dom';
import styled from 'styled-components';

import {CategoryTestApi, IResponseError} from '../../api/CategoryTestApi';
import {Button} from '../../components/Button';
import {Loader} from '../../components/Loader';
import {useQuery} from '../../hooks/useQuery';
import {IQuestion, QuestionCard} from './components/QuestionCard';
import {TestPreview} from './components/TestPreview';
import {TestWarningModal} from './components/TestWarningModal';
import {TestResult} from './components/TestResult';
import {ServerErrorMessage} from '../../components/ServerErrorMessage';

export interface ITest {
    id: number;
    status: string | null;
    category: {
        id: number;
        name: string;
        description: string;
        descriptionShort: string;
    };
    questions: IQuestion[];
}

interface IValues {
    [key: number]: Set<number>;
}

export const CategoryTest: React.FC = () => {
    const {categoryId} = useParams<{categoryId: string}>();
    const {data, isLoading, isError} = useQuery<ITest, IResponseError>(() => CategoryTestApi.getTest(categoryId));
    const [values, setValues] = useState<IValues>({});
    const [isTestVisible, setIsTestVisible] = useState<boolean>(false);
    const [isTestResultVisible, setIsTestResultVisible] = useState<boolean>(false);
    const [isTestWarningModalOpen, setIsTestWarningModalOpen] = useState<boolean>(false);
    const testRef = useRef<any>();
    const {id, questions = [], category} = data ?? {};

    useEffect(() => {
        const preventNav = (e: BeforeUnloadEvent) => {
            if (isTestVisible) {
                // e.preventDefault();
                // e.returnValue = '';
            }
        };

        window.addEventListener('beforeunload', preventNav);

        return () => {
            window.removeEventListener('beforeunload', preventNav);
        };
    }, [isTestVisible]);

    const goToTest = () => {
        setIsTestVisible(true);
        testRef.current.scrollIntoView({block: 'end', behavior: 'smooth'});
    };

    const getIsChecked = (questionId: number, answerId: number) => {
        return values[questionId] && values[questionId].has(answerId);
    };

    const changeValue = (questionId: number, answerId: number, isMultipleAnswers: boolean) => {
        let newValues = new Set(values[questionId] || []);

        if (isMultipleAnswers) {
            if (newValues.has(answerId)) {
                newValues.delete(answerId);
            } else {
                newValues.add(answerId);
            }
        } else {
            newValues = new Set([answerId]);
        }

        setValues((prev: any) => {
            return {...prev, [questionId]: newValues};
        });
    };

    const handleSubmit = () => {
        const ll = Object.values(values).reduce((prev: number[], current: number[]) => {
            return [...prev, ...current];
        }, []);
        setIsTestResultVisible(true);
    };

    if (isLoading) {
        return <Loader />;
    }

    if (isError) {
        return <ServerErrorMessage />;
    }

    return (
        <>
            {/*<Prompt when={isTestVisible} message='Завершить тест досрочно? Ответы не будут сохранены' />*/}

            <Container>
                <TestWarningModal isOpen={isTestWarningModalOpen} />
                {category && (
                    <TestPreview title={category?.description} goToTest={goToTest} isTestVisible={isTestVisible} />
                )}
                <TestContainer ref={testRef}>
                    {isTestVisible && questions.length > 0 && (
                        <>
                            <QuestionsList>
                                {questions.map((question: IQuestion, i: number) => {
                                    const isMultipleAnswers =
                                        question.answersCountToChooseMin !== question.answersCountToChooseMax;

                                    return (
                                        <QuestionCard
                                            key={question.id}
                                            id={question.id}
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
                </TestContainer>
                {isTestResultVisible && <TestResult isSuccess={false} />}
            </Container>
        </>
    );
};

const Container = styled.div`
    max-width: 566px;
    margin: 0 auto;
`;

const TestContainer = styled.div<{ref: any}>``;

const QuestionsList = styled.div`
    margin-bottom: 64px;
`;

const ButtonContainer = styled.div`
    text-align: center;
`;
