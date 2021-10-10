import {ITest} from '../pages/category_test/CategoryTest';
import axiosWithToken from './axios';

export interface IResponseError {
    code: string;
    message: string;
}

export const CategoryTestApi = {
    getTest(categoryId: string) {
        return axiosWithToken
            .post<ITest>(`${process.env.REACT_APP_API_URL}/tests`, {
                categoryId,
            })
            .then((response) => {
                const questions = response.data.questions.map((question) => {
                    if (question.answersCountToChooseMax) {
                        return question;
                    }
                    return {...question, answersCountToChooseMax: question.answers.length};
                });
                return {...response.data, questions};
            });
    },
    checkTest(testId: number, answers: number[]) {
        return axiosWithToken
            .patch<ITest>(`${process.env.REACT_APP_API_URL}/tests/${testId}/answers`, {answers})
            .then((response) => {
                return response.data;
            });
    },
};
