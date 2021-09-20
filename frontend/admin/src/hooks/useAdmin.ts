import {IAllTestsResponse, IFilterParams, ITest, ManagmentApi} from '../api/ManagmentApi';
import {useQuery} from './useQuery';

export const useAllTestsByParams = (params?: Partial<IFilterParams>) => {
    return useQuery<IAllTestsResponse>(() => ManagmentApi.getAllTestsByParams(params));
};

export const useUserTestById = (testId: string) => {
    return useQuery<ITest>(() => ManagmentApi.getTestById(testId));
};
