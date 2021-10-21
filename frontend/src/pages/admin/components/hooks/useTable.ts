import isEqual from 'lodash.isequal';
import {useState, useCallback, useMemo, useRef, useEffect} from 'react';

import {Status} from '../../../../api/ManagmentApi';
import {Option} from '../../AllTestsPage';

import {Search, useTableHistory} from './useTableHistory';

interface IUseTableFilter {
    isFiltered: boolean;
    statusOutline: boolean;
    onEmailSubmit: () => void;
}

interface ITableFilterData {
    status: Status[];
    dateStart: number;
    dateEnd: number;
    email: string;
}
interface ITableFilterParams {
    options: Option[];
    data: Partial<ITableFilterData>;
    resetTable: () => void;
}

export const useTableFilter = (params: ITableFilterParams): IUseTableFilter => {
    const {options, data, resetTable} = params;
    const {searchParams, onDeleteSearch} = useTableHistory();
    const isSearchParams = !!searchParams.get(Search.EMAIL);
    const [isEmailSubmit, setIsEmailSubmit] = useState<boolean>(isSearchParams);

    const onEmailSubmit = () => {
        setIsEmailSubmit(true);
    };

    const statusOutline = useRef<boolean>(true);

    const isFilterApply = useMemo(() => {
        let isFilter = false;

        if (data.dateEnd || data.dateStart) {
            isFilter = true;
        }

        if (!isEqual(data.status, options[0].value)) {
            isFilter = true;
            statusOutline.current = true;
        }

        if (isEqual(data.status, options[0].value)) {
            statusOutline.current = false;
            onDeleteSearch(Search.TABLE_STATUS);
        }

        if (data.email && isEmailSubmit) {
            isFilter = true;
        }

        if (isEmailSubmit && !data.email) {
            setIsEmailSubmit(false);
            if (!isFilter) {
                resetTable();
            }
        }

        return isFilter;
    }, [data, isEmailSubmit, options, resetTable, onDeleteSearch]);

    return {
        isFiltered: isFilterApply,
        onEmailSubmit,
        statusOutline: statusOutline.current,
    };
};

export type TPage = number | '';
interface ITablePage {
    page: TPage;
    onChangePage: (nextPage: TPage) => void;
}

export const useTablePage = (totalPages: number): ITablePage => {
    const {searchParams, onChangeSearch} = useTableHistory();
    const numberOfPage = Number(searchParams.get(Search.PAGE));
    const currentPage = (): number => {
        if (numberOfPage > 0 && numberOfPage <= totalPages) {
            return numberOfPage;
        }
        return 1;
    };

    const [page, setPage] = useState<TPage>(1);

    useEffect(() => {
        setPage(currentPage());
    }, [currentPage()]);

    const onChangePage = useCallback((nextPage: TPage) => {
        setPage(nextPage);
        onChangeSearch(Search.PAGE, String(nextPage));
    }, []);

    return {
        page: page,
        onChangePage,
    };
};
