import {IAllTestsResponse, IFilterParams, ManagmentApi} from '../api/ManagmentApi';
import {useQuery} from './useQuery';

export const useAllTestsByParams = (params?: Partial<IFilterParams>) => {
    return useQuery<IAllTestsResponse>(() => ManagmentApi.getAllTestsByParams(params));
};

// export const useUserTestById = (testId: number) => {
//     return useQuery(() => )
// }
