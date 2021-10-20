import {useCallback, useMemo} from 'react';
import {useHistory} from 'react-router-dom';

type TSearch = 'email' | 'tableStatus' | 'dateStart' | `dateEnd`;

export enum Search {
    EMAIL = 'email',
    TABLE_STATUS = 'tableStatus',
    DATE_START = 'dateStart',
    DATE_END = 'dateEnd',
}

interface IUseTableHistory {
    onChangeSearch: (name: TSearch, value: string) => void;
    onDeleteSearch: (name: TSearch) => void;
    searchParams: URLSearchParams;
}

export const useTableHistory = (): IUseTableHistory => {
    const {push, location} = useHistory();
    const searchParams = useMemo(() => new URLSearchParams(location.search), [location.search]);

    const onChangeSearch = useCallback(
        (name: TSearch, value: string) => {
            searchParams.set(name, value);
            push(`/tests?${searchParams}`);
        },
        [push, searchParams],
    );

    const onDeleteSearch = useCallback(
        (name: TSearch) => {
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
