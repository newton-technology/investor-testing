import isEqual from 'lodash.isequal';
import {useState, useCallback, useMemo, ChangeEvent, SyntheticEvent} from 'react';

import {Status} from '../api/ManagmentApi';
import {Option} from '../pages/admin/AllTestsPage';

interface IUseTableSearch {
    email: string | undefined;
    tableValue: string;
    value: string;
    onChangeTableValue: (value: string) => void;
    onChangeInputValue: (event: ChangeEvent<HTMLInputElement>) => void;
    OnInputValueSubmit: (callBack: () => void) => void;
    resetTableSearch: () => void;
}

export const useTableSearch = (initialValue: string = ''): IUseTableSearch => {
    const [inputValue, setInputValue] = useState<string>(initialValue);
    const [tableValue, setTableValue] = useState<string>(initialValue);

    const onChangeTableValue = useCallback((value: string) => {
        setTableValue(value);
        setInputValue('');
    }, []);

    const onChangeInputValue = useCallback(
        (event: ChangeEvent<HTMLInputElement>) => {
            if (tableValue) setTableValue('');
            setInputValue(event.target.value);
        },
        [tableValue],
    );

    const OnInputValueSubmit = (cb: () => void): void => {
        if (inputValue) cb();
    };

    const resetTableSearch = (): void => {
        setInputValue('');
        setTableValue('');
    };

    return {
        email: tableValue || inputValue || undefined,
        value: tableValue || inputValue,
        tableValue,
        onChangeTableValue,
        onChangeInputValue,
        OnInputValueSubmit,
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
    const [status, setStatus] = useState<Status[]>(initialValue);

    const statusHandler = useCallback((_, {value}) => {
        setStatus(value);
    }, []);

    const resetTableStatus = (): void => {
        setStatus(initialValue);
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
            dateStart: new Date(date.dateStart).setHours(0, 0) / 1000 || undefined,
            dateEnd: new Date(date.dateEnd).setHours(23, 59) / 1000 || undefined,
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
    const [isEmailSubmit, SetIsEmailSubmit] = useState<boolean>(false);

    const onEmailSubmit = () => {
        SetIsEmailSubmit(true);
    };

    let statusOutline: boolean = false;

    const isFilterApply = useMemo(() => {
        let isFilter = false;

        if (data.dateEnd || data.dateStart) {
            isFilter = true;
        }

        if (!isEqual(data.status, options[0].value)) {
            isFilter = true;
            statusOutline = true;
        }

        if (data.email && isEmailSubmit) {
            isFilter = true;
        }

        if (isEmailSubmit && !data.email) {
            SetIsEmailSubmit(false);
            if (!isFilter) {
                resetTable();
            }
        }

        return isFilter;
    }, [data, isEmailSubmit, options, resetTable]);

    return {
        isFiltered: isFilterApply,
        onEmailSubmit,
        statusOutline,
    };
};
