import React, {useRef, useState, useEffect} from 'react';
import {useParams} from 'react-router-dom';
import styled from 'styled-components';

import {CategoryTestApi, IResponseError} from '../../api/CategoryTestApi';
import {Button} from '../../components/Button';
import {ErrorMessage} from '../../components/ErrorMessage';
import {Loader} from '../../components/Loader';
import {useMutation} from '../../hooks/useMutation';
import {useQuery} from '../../hooks/useQuery';
import {useScrollToTop} from '../../hooks/useScrollToTop';
import MoExBanner from './components/MoExBanner';
import {IQuestion, QuestionCard} from './components/QuestionCard';
import {TestPreview} from './components/TestPreview';
import {TestResult} from './components/TestResult';
import {TestWarningModal} from './components/TestWarningModal';
import {getAllAnswers, getAnswersCountMessage, validate} from './utils';

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

export interface IValues {
    [key: number]: Set<number>;
}

export const CategoryTest: React.FC = () => {
    const {categoryId} = useParams<{categoryId: string}>();

    const {data, isLoading, isError, error} = useQuery<ITest, IResponseError>(() => {
        return CategoryTestApi.getTest(categoryId);
    });
    const [values, setValues] = useState<IValues>({});
    const [incorrectQuestionId, setIncorrectQuestionId] = useState<number | undefined>();
    const [isTestVisible, setIsTestVisible] = useState<boolean>(false);
    const [isTestResultVisible, setIsTestResultVisible] = useState<boolean>(false);
    const testRef = useRef<HTMLDivElement>();
    const {id, questions = [], category} = data ?? {};

    const checkTestMutation = useMutation(() => CategoryTestApi.checkTest(id as number, getAllAnswers(values)), {
        onSuccess: () => {
            setIsTestResultVisible(true);
            setIncorrectQuestionId(undefined);
        },
    });

    useScrollToTop([checkTestMutation.isError]);

    useEffect(() => {
        if (isTestVisible && testRef.current) {
            testRef.current.scrollIntoView({behavior: 'smooth'});
        }
    }, [isTestVisible]);

    const goToTest = () => {
        setIsTestVisible(true);
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

    const handleSubmit = async () => {
        const incorrectId = validate(questions, values);

        if (!incorrectId) {
            await checkTestMutation.mutate();
        } else {
            setIncorrectQuestionId(incorrectId);
        }
    };

    if (isLoading) {
        return <Loader />;
    }

    if (isError || checkTestMutation.isError) {
        if (error?.code === 'category_passed') {
            return <TestResult isSuccess={true} />;
        }
        return <ErrorMessage />;
    }

    return (
        <Container>
            <TestWarningModal isBlocked={isTestVisible && !isTestResultVisible} />
            {category && (
                <TestPreview title={category?.description} goToTest={goToTest} isTestVisible={isTestVisible} />
            )}
            {isTestVisible && (
                <TestContainer ref={testRef} isTestComplete={isTestResultVisible}>
                    <QuestionsList>
                        {questions.map((question: IQuestion, i: number) => {
                            const isMultipleAnswers = !(
                                question.answersCountToChooseMin === 1 && question.answersCountToChooseMax === 1
                            );

                            const answersCountMessage = getAnswersCountMessage(
                                question.answersCountToChooseMin,
                                question.answersCountToChooseMax,
                                question.answers.length,
                            );

                            return (
                                <QuestionCard
                                    key={question.id}
                                    id={question.id}
                                    title={question.question}
                                    answers={question.answers}
                                    getIsChecked={getIsChecked}
                                    changeValue={changeValue}
                                    questionsCount={questions.length}
                                    answersCountMessage={answersCountMessage}
                                    index={i + 1}
                                    isMultipleAnswers={isMultipleAnswers}
                                    isError={incorrectQuestionId === question.id}
                                />
                            );
                        })}
                    </QuestionsList>
                    {!isTestResultVisible && (
                        <ButtonContainer>
                            <Button onClick={handleSubmit} isLoading={checkTestMutation.isLoading}>
                                Завершить тест
                            </Button>
                        </ButtonContainer>
                    )}
                </TestContainer>
            )}
            {isTestResultVisible && (
                <>
                    <TestResult isSuccess={checkTestMutation.data?.status === 'passed'} />
                    <MoExBanner />
                </>
            )}
        </Container>
    );
};

const Container = styled.div`
    max-width: 566px;
    margin: 0 auto;
`;

const TestContainer = styled.div<{ref: any; isTestComplete: boolean}>`
    position: relative;

    ${({isTestComplete}) =>
        isTestComplete &&
        `
        opacity: .3;
        
        &:before {
            display: block;
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: 1;
        }
    `}
`;

const QuestionsList = styled.div`
    margin-bottom: 64px;
`;

const ButtonContainer = styled.div`
    text-align: center;
`;
