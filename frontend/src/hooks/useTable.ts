import isEqual from 'lodash.isequal';
import {useState, useCallback, useMemo, ChangeEvent, SyntheticEvent, useRef} from 'react';

import {Status} from '../api/ManagmentApi';
import {Option} from '../pages/admin/AllTestsPage';
import {unixTime} from '../utils/tableUtils';
import {Search, useTableHistory} from './useTableHistory';

interface IUseTableSearch {
    email: string | undefined;
    tableValue: string;
    value: string;
    onChangeTableValue: (value: string) => void;
    onChangeInputValue: (event: ChangeEvent<HTMLInputElement>) => void;
    onInputValueSubmit: (callBack: () => void) => void;
    resetTableSearch: () => void;
}

export const useTableSearch = (initialValue: string = ''): IUseTableSearch => {
    const {onChangeSearch, onDeleteSearch, searchParams} = useTableHistory();
    const email = searchParams.get(Search.EMAIL);
    const [inputValue, setInputValue] = useState<string>(email ? email : initialValue);
    const [tableValue, setTableValue] = useState<string>(initialValue);

    const onChangeTableValue = useCallback((value: string) => {
        onChangeSearch(Search.EMAIL, value);
        setTableValue(value);
        setInputValue('');
    }, []);

    const onChangeInputValue = useCallback(
        (event: ChangeEvent<HTMLInputElement>) => {
            if (tableValue) setTableValue('');
            onChangeSearch(Search.EMAIL, event.target.value);
            setInputValue(event.target.value);
        },
        [tableValue],
    );

    const onInputValueSubmit = (cb: () => void): void => {
        if (inputValue) cb();
    };

    const resetTableSearch = () => {
        setInputValue('');
        setTableValue('');
        onDeleteSearch(Search.EMAIL);
    };

    return {
        email: tableValue || inputValue || undefined,
        value: tableValue || inputValue,
        tableValue,
        onChangeTableValue,
        onChangeInputValue,
        onInputValueSubmit,
        resetTableSearch,
    };
};

interface IUseTableStatus {
    status: Status[];
    setStatus: (value: Status[]) => void;
    statusHandler: (event: SyntheticEvent, data: {title: string; value: Status[]}) => void;
    resetTableStatus: () => void;
}

const statusInitialValue = [Status.PASSED, Status.FAILED];

export const useTableStatus = (initialValue: Status[] = statusInitialValue): IUseTableStatus => {
    const {onChangeSearch, onDeleteSearch, searchParams} = useTableHistory();
    const tableStatus = searchParams.get(Search.TABLE_STATUS);
    const statusArr: string[] | null = tableStatus ? tableStatus.split('-') : null;
    if (statusArr) {
        const keys = Object.keys(Status);
        const result: Status[] = [];
        for (const element of statusArr) {
            const key = keys.find((x) => {
                return Status[x as keyof typeof Status] === element;
            });
            if (key) result.push(Status[key as keyof typeof Status]);
        }
        if (result.length > 0) {
            initialValue = result;
        }
    }

    const [status, setStatus] = useState<Status[]>(initialValue);

    const statusHandler = (_: SyntheticEvent, {value}: {value: Status[]}) => {
        setStatus(value);
        onChangeSearch(Search.TABLE_STATUS, value.join('-'));
    };

    const resetTableStatus = (): void => {
        setStatus(statusInitialValue);
        onDeleteSearch(Search.TABLE_STATUS);
    };

    return {
        status,
        setStatus,
        statusHandler,
        resetTableStatus,
    };
};

export type TDate = {dateStart: string; dateEnd: string};
type TFormattedDates = {dateStart: number | undefined; dateEnd: number | undefined};

interface IUseTableDates {
    datesValue: TDate;
    formattedDates: TFormattedDates;
    onDateChange: (event: ChangeEvent<HTMLInputElement>) => void;
    clearTableDates: () => void;
}

export const useTableDates = (): IUseTableDates => {
    const [date, setDate] = useState<TDate>({dateStart: '', dateEnd: ''});

    const dates = useMemo<TFormattedDates>(() => {
        return {
            dateStart: unixTime(date.dateStart, '00:00') || undefined,
            dateEnd: unixTime(date.dateEnd, '23:59') || undefined,
        };
    }, [date]);

    const onDateChange = useCallback((event: ChangeEvent<HTMLInputElement>) => {
        setDate((prev) => ({...prev, [event.target.name]: event.target.value}));
    }, []);

    const clearTableDates = (): void => {
        setDate({dateStart: '', dateEnd: ''});
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
    const isSearchParams = !!searchParams.get('email');
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
    }, [data, isEmailSubmit, options, resetTable]);

    return {
        isFiltered: isFilterApply,
        onEmailSubmit,
        statusOutline: statusOutline.current,
    };
};
