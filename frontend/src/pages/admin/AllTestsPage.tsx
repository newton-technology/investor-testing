import React, {useState, useCallback, ChangeEvent, useEffect} from 'react';
import styled from 'styled-components';

import {Status} from '../../api/ManagmentApi';
import {useAllTestsByParams} from '../../hooks/useAdmin';
import DatePicker from './components/DatePicker';
import Paginator from './components/Paginator';
import SearchInput from './components/SearchInput';
import Select from './components/Select';
import TestsTable from './components/TestTable/TestsTable';

export type Option = {
    title: string;
    value: Status[];
};

const options: Option[] = [
    {title: 'Все тесты', value: [Status.PASSED, Status.FAILED]},
    {title: 'Только успешные', value: [Status.PASSED]},
];

export const AllTestsPage: React.FC = () => {
    const [inputValue, setInputValue] = useState<string>('');
    const [tableValue, setTablueValue] = useState<string>('');
    const [status, setStatus] = useState<Status[]>(options[0].value);
    const [page, setPage] = useState<number | string>(0);

    const email = tableValue || inputValue || undefined;

    const {
        data: tests = [],
        isLoading,
        refetch,
    } = useAllTestsByParams({limit: 20, offset: Number(page) || undefined, status: status, email});

    const selectEmail = (value: string) => {
        setTablueValue(value);
        setInputValue('');
    };

    const selectHandler = useCallback((_, {value}) => {
        setStatus(value);
    }, []);

    const onChange = (event: ChangeEvent<HTMLInputElement>) => {
        if (tableValue) setTablueValue('');
        setInputValue(event.target.value);
    };

    const onChangePage = (nextPage: number | string) => {
        setPage(nextPage);
    };

    const onSubmit = () => {
        if (inputValue) refetch();
        setInputValue('');
    };

    useEffect(() => {
        if (tableValue) refetch();
    }, [status, tableValue, page]);

    return (
        <>
            <FiltersWrapper>
                <SearchInput
                    onChange={onChange}
                    onSubmit={onSubmit}
                    value={tableValue || inputValue}
                    placeholder='        Поиск по email'
                />
                <DatePicker />
                <Select options={options} value={status} onChange={selectHandler} />
            </FiltersWrapper>
            <ResultSection>
                Найдено: <ResultCount>{tests.length}</ResultCount> совпадений
            </ResultSection>
            <TestsTable isLoading={isLoading} tests={tests} selectEmail={selectEmail} />
            <Paginator onChangePage={onChangePage} currentPage={page + 1} maxPage={10} />
        </>
    );
};

const FiltersWrapper = styled.div`
    align-items: center;
    display: flex;
    margin-bottom: 32px;
    width: 100%;

    & > div:not(first-child) {
        margin-left: 32px;
    }
`;

const ResultSection = styled.span`
    color: ${({theme}) => theme.palette.bg.darkBlue};
    display: block;
    margin-bottom: 32px;
    width: 100%;
`;

const ResultCount = styled.span`
    font-weight: bold;
`;
