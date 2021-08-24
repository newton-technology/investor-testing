import axiosWithToken from './axios';
import {ITest} from '../pages/category_test/CategoryTest';

export const CategoryListApi = {
    getCategories() {
        return axiosWithToken.get<ITest[]>(`${process.env.REACT_APP_API_URL}/categories`).then((response) => {
            return response.data;
        });
    },
};
