import {useEffect, useState} from 'react';

interface IState {
    data: any;
    isLoading: boolean;
    isError: boolean;
}

export const useQuery = (query: any) => {
    const [state, setState] = useState<IState>({data: null, isLoading: true, isError: false});

    useEffect(() => {
        const handleQuery = async () => {
            await query()
                .then((response: any) => {
                    setState({data: response, isLoading: false, isError: false});
                })
                .catch((error: any) => {
                    setState({data: null, isLoading: false, isError: true});
                });
        };
        handleQuery();
    }, []);

    return state;
};
