import axiosWithToken from './axios';

export enum Status {
    PASSED = 'passed',
    FAILED = 'failed',
    PROCESSING = 'processing',
    DRAFT = 'draft',
    CANCELED = 'canceled',
}

export enum Sort {
    UPDATED_ASC = 'updatedAt,asc',
    UPDATED_DESC = 'updatedAt,desc',
    CREATED_ASC = 'createdAt,asc',
    CREATED_DESC = 'createdAt,desc',
}

export interface IFilterParams {
    status: Status[];
    dateStart: number;
    dateEnd: number;
    email: string;
    limit: number;
    offset: number;
    sort: Sort[];
}

export interface IAllTestsResponse {
    userId: number;
    userEmail: string;
    id: number;
    createdAt: number;
    updatedAt: number;
    category: {
        id: number;
        description: string;
        descriptionShort: string;
    };
    status: Status;
}

export const ManagmentApi = {
    getAllTestsByParams(filterParams?: Partial<IFilterParams>): Promise<IAllTestsResponse[]> {
        return axiosWithToken
            .get<IAllTestsResponse[]>(`${process.env.REACT_APP_API_URL}/management/tests`, {params: filterParams})
            .then((response) => {
                console.log(response);
                return response.data;
            });
    },
};
