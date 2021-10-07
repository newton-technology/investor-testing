import {useState, useCallback, useMemo, ChangeEvent, SyntheticEvent} from 'react';

import {Status} from '../api/ManagmentApi';

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
    onEmailSubmit: () => void;
}

interface ITableFilterParams {
    status: Status[];
    dateStart: number;
    dateEnd: number;
    email: string;
}

export const useTableFilter = (params: Partial<ITableFilterParams>): IUseTableFilter => {
    console.log(params);
    const [isFiltered, setIsFiltered] = useState<boolean>(false);

    const onEmailSubmit = () => {
        console.log(`log`);
    };

    Object.keys(params).forEach((key: string) => {
        // setIsFiltered(false);
        // if (params[key]) {
        //     setIsFiltered(true);
        // }
    });

    return {
        isFiltered: isFiltered,
        onEmailSubmit,
    };
};
