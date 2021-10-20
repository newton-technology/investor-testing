import {useCallback, useMemo} from 'react';
import {useHistory} from 'react-router-dom';

type SearchType = 'email' | 'tableStatus' | 'dateStart' | `dateEnd`;

export enum Search {
    EMAIL = 'email',
    TABLE_STATUS = 'tableStatus',
    DATE_START = 'dateStart',
    DATE_END = 'dateEnd',
}

interface IUseTableHistory {
    onChangeSearch: (name: SearchType, value: string) => void;
    onDeleteSearch: (name: SearchType) => void;
    searchParams: URLSearchParams;
}

export const useTableHistory = (): IUseTableHistory => {
    const {push, location} = useHistory();
    const searchParams = useMemo(() => new URLSearchParams(location.search), [location.search]);

    const onChangeSearch = useCallback(
        (name: SearchType, value: string) => {
            searchParams.set(name, value);
            push(`/tests?${searchParams}`);
        },
        [push, searchParams],
    );

    const onDeleteSearch = useCallback(
        (name: SearchType) => {
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
