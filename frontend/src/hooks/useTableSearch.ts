import {useState, useCallback, ChangeEvent} from 'react';

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

    const onChangeTableValue = useCallback(
        (value: string) => {
            onChangeSearch(Search.EMAIL, value);
            setTableValue(value);
            setInputValue('');
        },
        [onChangeSearch],
    );

    const onChangeInputValue = useCallback(
        (event: ChangeEvent<HTMLInputElement>) => {
            if (tableValue) setTableValue('');
            onChangeSearch(Search.EMAIL, event.target.value);
            setInputValue(event.target.value);
        },
        [tableValue, onChangeSearch],
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
