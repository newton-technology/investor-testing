import {ICategory} from '../pages/category_list/CategoryList';
import axiosWithToken from './axios';

export const CategoryListApi = {
    getCategories() {
        return axiosWithToken.get<ICategory[]>(`${process.env.REACT_APP_API_URL}/categories`).then((response) => {
            return response.data;
        });
    },
};
