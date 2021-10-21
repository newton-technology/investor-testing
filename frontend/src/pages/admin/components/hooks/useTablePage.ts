import {useState, useCallback, useEffect} from 'react';

import {Search, useTableHistory} from './useTableHistory';

export type TPage = number | '';
interface ITablePage {
    page: TPage;
    onChangePage: (nextPage: TPage) => void;
}

export const useTablePage = (totalPages: number): ITablePage => {
    const {searchParams, onChangeSearch} = useTableHistory();
    const numberOfPage = Number(searchParams.get(Search.PAGE));
    const currentPage = useCallback((): number => {
        if (numberOfPage > 0 && numberOfPage <= totalPages) {
            return numberOfPage;
        }
        return 1;
    }, [numberOfPage, totalPages]);

    const [page, setPage] = useState<TPage>(1);

    useEffect(() => {
        setPage(currentPage());
    }, [currentPage]);

    const onChangePage = useCallback(
        (nextPage: TPage) => {
            setPage(nextPage);
            onChangeSearch(Search.PAGE, String(nextPage));
        },
        [onChangeSearch],
    );

    return {
        page: page,
        onChangePage,
    };
};
