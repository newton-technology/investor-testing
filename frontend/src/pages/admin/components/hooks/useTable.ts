import isEqual from 'lodash.isequal';
import {useState, useCallback, useMemo, ChangeEvent, useRef, useEffect} from 'react';

import {Status} from '../../../../api/ManagmentApi';
import {Option} from '../../AllTestsPage';
import {unixTime} from '../../../../utils/tableUtils';
import {Search, TSearch, useTableHistory} from './useTableHistory';

export type TDate = {dateStart: string; dateEnd: string};
type TFormattedDates = {dateStart: number | undefined; dateEnd: number | undefined};

interface IUseTableDates {
    datesValue: TDate;
    formattedDates: TFormattedDates;
    onDateChange: (event: ChangeEvent<HTMLInputElement>) => void;
    clearTableDates: () => void;
}

export const useTableDates = (): IUseTableDates => {
    const {onChangeSearch, onDeleteSearch, searchParams} = useTableHistory();
    const dateStart = searchParams.get(Search.DATE_START) || '';
    const dateEnd = searchParams.get(Search.DATE_END) || '';
    const isDateStart = /\d{4}-\d{2}-\d{2}/.test(dateStart);
    const isDateEnd = /\d{4}-\d{2}-\d{2}/.test(dateEnd);
    const [date, setDate] = useState<TDate>({
        dateStart: isDateStart ? dateStart : '',
        dateEnd: isDateEnd ? dateEnd : '',
    });
    const dates = useMemo<TFormattedDates>(() => {
        return {
            dateStart: unixTime(date.dateStart, '00:00') || undefined,
            dateEnd: unixTime(date.dateEnd, '23:59') || undefined,
        };
    }, [date]);

    const onDateChange = useCallback(
        (event: ChangeEvent<HTMLInputElement>) => {
            setDate((prev) => ({...prev, [event.target.name]: event.target.value}));
            onChangeSearch(event.target.name as TSearch, event.target.value);
        },
        [onChangeSearch],
    );

    const clearTableDates = (): void => {
        setDate({dateStart: '', dateEnd: ''});
        onDeleteSearch(Search.DATE_START);
        onDeleteSearch(Search.DATE_END);
    };

    return {
        datesValue: date,
        formattedDates: dates,
        onDateChange,
        clearTableDates,
    };
};

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
