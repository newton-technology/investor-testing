import axios, {AxiosError} from 'axios';

import {useEffect, useState} from 'react';
import {IResponseError} from '../api/CategoryTestApi';

type IState<QueryData, QueryError> = {
    data: QueryData | undefined;
    error: QueryError | AxiosError<IResponseError> | undefined;
    isLoading: boolean;
    isError: boolean;
};

export function useQuery<QueryData = unknown, QueryError = Error>(query: () => Promise<QueryData>) {
    const [state, setState] = useState<IState<QueryData, QueryError>>({
        data: undefined,
        isLoading: true,
        isError: false,
        error: undefined,
    });

    const handleQuery = () => {
        query()
            .then((response) => {
                setState({...state, data: response, isLoading: false});
            })
            .catch((error: QueryError | AxiosError<QueryError>) => {
                if (axios.isAxiosError(error)) {
                    setState({...state, isLoading: false, isError: true, error: error.response?.data});
                } else {
                    setState({...state, isLoading: false, isError: true, error: error});
                }
            });
    };

    const refetch = () => {
        setState({...state, isLoading: true});
        handleQuery();
    };

    useEffect(() => {
        handleQuery();
    }, []);

    return {...state, refetch};
}
