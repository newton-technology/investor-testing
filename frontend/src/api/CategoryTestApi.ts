import axiosWithToken from './axios';

export const CategoryTestApi = {
    getTest(categoryId: string) {
        return axiosWithToken.post(`${process.env.REACT_APP_API_URL}/tests`, {categoryId}).then((response) => {
            console.log(response);
        });
    },
    checkTest(testId: number, answers: number[]) {
        return axiosWithToken
            .patch(`${process.env.REACT_APP_API_URL}/tests/${testId}/answers`, {answers})
            .then((response) => {
                console.log(response);
            });
    },
};
