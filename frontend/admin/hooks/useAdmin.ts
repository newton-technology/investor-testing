import {IAllTestsResponse, IFilterParams, ManagmentApi} from '../api/ManagmentApi';
import {ITest} from '../pages/category_test/CategoryTest';
import {useQuery} from './useQuery';

export const useAllTestsByParams = (params?: Partial<IFilterParams>) => {
    return useQuery<IAllTestsResponse>(() => ManagmentApi.getAllTestsByParams(params));
};

export const useUserTestById = (testId: string) => {
    return useQuery<ITest>(() => ManagmentApi.getTestById(testId));
};
