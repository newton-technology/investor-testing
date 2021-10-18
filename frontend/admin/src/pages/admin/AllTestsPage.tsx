import React, {useState, useCallback, useEffect, useMemo, useRef} from 'react';
import {useLocation} from 'react-router-dom';
import styled from 'styled-components';

import {Sort, Status} from '../../api/ManagmentApi';
import {useAllTestsByParams} from '../../hooks/useAdmin';
import {useTableDates, useTableSearch, useTableStatus, useTableFilter} from '../../hooks/useTable';
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
    {title: 'Провальные', value: [Status.FAILED]},
];

export type TPage = number | '';
const limitPerRequest = 20;
const reponseDefaultValue = {tests: [], limit: 0, offset: 0, total: 0};

export const AllTestsPage: React.FC = () => {
    const location = useLocation();
    const searchParams = new URLSearchParams(location.search);

    const {email, tableValue, value, onChangeInputValue, onChangeTableValue, OnInputValueSubmit, resetTableSearch} =
        useTableSearch(searchParams);
    const {status, statusHandler, resetTableStatus} = useTableStatus();
    const {datesValue, formattedDates, onDateChange, clearTableDates} = useTableDates();
    const [page, setPage] = useState<TPage>(1);
    const [sort, setSort] = useState<Sort>(Sort.COMPLETED_DESC);
    const isInitialRender = useRef<boolean>(true);

    const offsetValue = useMemo(() => {
        return page !== '' ? (page - 1) * limitPerRequest : 0;
    }, [page]);

    const {
        data = reponseDefaultValue,
        isLoading,
        refetch,
    } = useAllTestsByParams({
        limit: limitPerRequest,
        offset: offsetValue,
        status: status,
        email: email ? `%${email}%` : undefined,
        sort: [sort],
        ...formattedDates,
    });

    const {tests, total} = data;
    const totalPages = Math.ceil(total / limitPerRequest);

    const onChangePage = useCallback((nextPage: TPage) => {
        setPage(nextPage);
    }, []);

    const reset = useCallback(() => {
        resetTableSearch();
        clearTableDates();
        resetTableStatus();
        setPage(1);
    }, [resetTableSearch, clearTableDates, resetTableStatus]);

    const {onEmailSubmit, isFiltered, statusOutline} = useTableFilter({
        options: options,
        searchParams: searchParams,
        data: {
            status: status,
            email: email,
            ...formattedDates,
        },
        resetTable: reset,
    });

    const onSearchSubmit = () => {
        OnInputValueSubmit(refetch);
        onEmailSubmit();
    };

    const onChangeTableSubmit = (e: string) => {
        onChangeTableValue(e);
        onEmailSubmit();
    };

    useEffect(() => {
        if (!isInitialRender.current) {
            refetch();
            window.scrollTo(0, 0);
        }
    }, [status, page, tableValue, formattedDates, sort]);

    useEffect(() => {
        isInitialRender.current = false;
    }, []);

    return (
        <>
            <FiltersWrapper>
                <SearchInput
                    onChange={onChangeInputValue}
                    onSubmit={onSearchSubmit}
                    value={value}
                    placeholder='        Поиск по email'
                />
                <DatePicker date={datesValue} dateHandler={onDateChange} clear={clearTableDates} />
                <Select options={options} value={status} onChange={statusHandler} outline={statusOutline} />
            </FiltersWrapper>
            <ResultSection>
                Найдено: <ResultCount>{total}</ResultCount> совпадений
                {isFiltered && <ShowAllResultsButton onClick={reset}>Очистить результаты поиска</ShowAllResultsButton>}
            </ResultSection>
            <TestsTable
                isLoading={isLoading}
                tests={tests}
                selectEmail={onChangeTableSubmit}
                sort={sort}
                setSort={setSort}
                filter={value}
            />
            {!!tests.length && <Paginator onChangePage={onChangePage} currentPage={page} maxPage={totalPages} />}
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

const ShowAllResultsButton = styled.span`
    color: ${({theme}) => theme.palette.primary};
    cursor: pointer;
    font-size: 17px;
    font-weight: normal;
    line-height: 130%;
    margin-left: 8px;
`;
