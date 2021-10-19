import {useCallback} from 'react';
import {useHistory} from 'react-router-dom';

interface IUseTableHistory {
    onChangeSearch: (name: string, value: string) => void;
    onDeleteSearch: (value: string) => void;
    searchParams: URLSearchParams;
}

export const useTableHistory = (): IUseTableHistory => {
    const {push, location} = useHistory();
    const searchParams = new URLSearchParams(location.search);

    const onChangeSearch = useCallback(
        (name: string, value: string) => {
            searchParams.set(name, value);
            push(`/tests?${searchParams}`);
        },
        [history, searchParams],
    );

    const onDeleteSearch = useCallback(
        (name: string) => {
            if (searchParams.get(name)) {
                searchParams.delete(name);
                push(`/tests?${searchParams}`);
            }
        },
        [history, searchParams],
    );

    return {
        onChangeSearch,
        onDeleteSearch,
        searchParams,
    };
};
