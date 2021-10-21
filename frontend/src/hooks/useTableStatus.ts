import {useState, SyntheticEvent} from 'react';

import {Status} from '../api/ManagmentApi';
import {Search, useTableHistory} from './useTableHistory';

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
