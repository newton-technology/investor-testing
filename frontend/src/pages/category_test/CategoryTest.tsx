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
import {getAllAnswers, validate} from './utils';
import {useMutation} from '../../hooks/useMutation';

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
    const [isTestWarningModalOpen, setIsTestWarningModalOpen] = useState<boolean>(false);
    const testRef = useRef<HTMLDivElement>();
    const {id, questions = [], category} = data ?? {};

    const checkTestMutation = useMutation(() => CategoryTestApi.checkTest(id as number, getAllAnswers(values)), {
        onSuccess: () => {
            setIsTestResultVisible(true);
            setIncorrectQuestionId(undefined);
        },
        onError: () => {
            debugger;
        },
    });

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
        const questionsIds = questions.map((question: IQuestion) => question.id);
        const incorrectId = validate(values, questionsIds);

        if (!incorrectId) {
            await checkTestMutation.mutate();
        } else {
            setIncorrectQuestionId(incorrectId);
        }
    };

    if (isLoading) {
        return <Loader />;
    }

    if (isError) {
        if (error?.code === 'category_passed') {
            return <TestResult isSuccess={true} />;
        }
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
                {isTestVisible && (
                    <TestContainer ref={testRef} isTestComplete={isTestResultVisible}>
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
                {isTestResultVisible && <TestResult isSuccess={checkTestMutation.data?.status === 'passed'} />}
            </Container>
        </>
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
