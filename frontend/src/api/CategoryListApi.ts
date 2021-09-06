import axiosWithToken from './axios';
import {ICategory} from '../pages/category_list/CategoryList';

export const CategoryListApi = {
    getCategories() {
        return axiosWithToken.get<ICategory[]>(`${process.env.REACT_APP_API_URL}/categories`).then((response) => {
            return response.data;
        });
    },
};
