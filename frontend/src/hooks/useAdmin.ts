import {useMemo} from 'react';

import {IAllTestsResponse, IFilterParams, ManagmentApi} from '../api/ManagmentApi';
import {useQuery} from './useQuery';

export const useAllTestsByParams = (params?: Partial<IFilterParams>) => {
    return useQuery<IAllTestsResponse[]>(() => ManagmentApi.getAllTestsByParams(params));
};
