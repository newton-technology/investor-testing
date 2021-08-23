import axiosWithToken from './axios';

export const CategoryTestApi = {
    getTest(categoryId: string) {
        return axiosWithToken.post(`${process.env.REACT_APP_API_URL}/tests`, {categoryId}).then((response) => {
            return response.data;
        });
    },
    checkTest(testId: string, answers: number[]) {
        return axiosWithToken
            .patch(`${process.env.REACT_APP_API_URL}/tests/${testId}/answers`, {answers})
            .then((response) => {
                return response.data;
            });
    },
};
