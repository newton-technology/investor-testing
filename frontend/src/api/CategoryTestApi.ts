import axios from './axios';

export const CategoryTestApi = {
    getTest(categoryId: string) {
        return axios.post(`${process.env.REACT_APP_API_URL}/tests`, {categoryId}).then((response) => {
            console.log(response);
        });
    },
};
