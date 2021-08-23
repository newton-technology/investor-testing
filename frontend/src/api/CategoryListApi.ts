import axiosWithToken from './axios';

export const CategoryListApi = {
    getCategories() {
        return axiosWithToken.get(`${process.env.REACT_APP_API_URL}/categories`).then((response) => {
            console.log(response);
        });
    },
};
