import {useState} from 'react';

type IState<QueryData, QueryError> = {
    data: QueryData | undefined;
    error: QueryError | undefined;
    isLoading: boolean;
    isError: boolean;
};

interface ICallbacks {
    onSuccess?: () => void;
    onError?: () => void;
}

export function useMutation<QueryData = unknown, QueryError = Error>(
    query: () => Promise<QueryData>,
    callbacks?: ICallbacks,
) {
    const [state, setState] = useState<IState<QueryData, QueryError>>({
        data: undefined,
        isLoading: false,
        isError: false,
        error: undefined,
    });
    const {onSuccess, onError} = callbacks || {};

    const mutate = async () => {
        setState({...state, isLoading: true});

        return await query()
            .then((response) => {
                setState({...state, data: response, isLoading: false});
                if (onSuccess) {
                    onSuccess();
                }
            })
            .catch((error) => {
                setState({...state, isLoading: false, isError: true, error: error});
                if (onError) {
                    onError();
                }
            });
    };

    return {...state, mutate};
}
