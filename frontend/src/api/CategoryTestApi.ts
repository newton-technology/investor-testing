import axiosWithToken from './axios';
import {ITest} from '../pages/category_test/CategoryTest';

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
                return response.data;
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
