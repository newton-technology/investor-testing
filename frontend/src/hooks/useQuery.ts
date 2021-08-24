import {useEffect, useState} from 'react';

type IState<QueryData, QueryError> = {
    data: QueryData | undefined;
    error: QueryError | undefined;
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

    useEffect(() => {
        const handleQuery = async () => {
            await query()
                .then((response) => {
                    setState({...state, data: response, isLoading: false});
                })
                .catch((error) => {
                    setState({...state, isLoading: false, isError: true, error: error});
                });
        };
        handleQuery();
    }, []);

    return state;
}
