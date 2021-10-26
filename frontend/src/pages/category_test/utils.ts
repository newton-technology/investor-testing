import {IValues} from './CategoryTest';
import {IQuestion} from './components/QuestionCard';

export const validate = (questions: IQuestion[], answers: IValues): number | undefined => {
    for (const question of questions) {
        const selected = answers[question.id]?.size ?? 0;
        const min = question.answersCountToChooseMin;
        const max = question.answersCountToChooseMax;

        if (min === 0) {
            continue;
        }
        if (selected === 0) {
            return +question.id;
        }
        if (selected < min || selected > max) {
            return +question.id;
        }
    }
};

export const getAllAnswers = (values: IValues) => {
    return Object.values(values).reduce((prev: number[], current: number[]) => {
        return [...prev, ...current];
    }, []);
};

export const getAnswersCountMessage = (min: number, max: number, all: number) => {
    if (min === 0) {
        return;
    }
    if (min === 1 && max === 1) {
        return '(возможен один вариант ответа)';
    }
    if (min && max && max !== all) {
        return `(возможно от ${min} до ${max} вариантов ответа)`;
    }
    return '(возможно несколько вариантов ответа)';
};
