import {useCallback, useMemo} from 'react';
import {useHistory, useLocation} from 'react-router-dom';

export type TSearch = 'email' | 'tableStatus' | 'dateStart' | 'dateEnd' | 'page';

export enum Search {
    EMAIL = 'email',
    TABLE_STATUS = 'tableStatus',
    DATE_START = 'dateStart',
    DATE_END = 'dateEnd',
    PAGE = 'page',
}

interface IUseTableHistory {
    onChangeSearch: (name: TSearch, value: string) => void;
    onDeleteSearch: (name: TSearch) => void;
    searchParams: URLSearchParams;
}

export const useTableHistory = (): IUseTableHistory => {
    const {push} = useHistory();
    const location = useLocation();
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
