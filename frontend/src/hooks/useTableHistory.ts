import {useCallback, useMemo} from 'react';
import {useHistory} from 'react-router-dom';

type Search = 'email' | 'tableStatus' | 'dateStart' | `dateEnd`;

interface IUseTableHistory {
    onChangeSearch: (name: Search, value: string) => void;
    onDeleteSearch: (name: Search) => void;
    searchParams: URLSearchParams;
}

export const useTableHistory = (): IUseTableHistory => {
    const {push, location} = useHistory();
    const searchParams = useMemo(() => new URLSearchParams(location.search), [location]);

    const onChangeSearch = useCallback(
        (name: string, value: string) => {
            searchParams.set(name, value);
            push(`/tests?${searchParams}`);
        },
        [push, searchParams],
    );

    const onDeleteSearch = useCallback(
        (name: string) => {
            if (searchParams.get(name)) {
                searchParams.delete(name);
                push(`/tests?${searchParams}`);
            }
        },
        [push, searchParams],
    );

    return {
        onChangeSearch,
        onDeleteSearch,
        searchParams,
    };
};
