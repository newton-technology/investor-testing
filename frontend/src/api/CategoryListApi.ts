import axios from './axios';

export const CategoryListApi = {
    getCategories() {
        return axios.get(`${process.env.REACT_APP_API_URL}/categories`).then((response) => {
            console.log(response);
        });
    },
};
