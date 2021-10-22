import {useState, useCallback, useMemo, ChangeEvent} from 'react';

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
