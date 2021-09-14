import React, {useState, useCallback, ChangeEvent, useEffect, useMemo, useRef} from 'react';
import styled from 'styled-components';

import {Sort, Status} from '../../api/ManagmentApi';
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
    {title: 'Провальные', value: [Status.FAILED]},
];

export type TDate = {dateStart: ''; dateEnd: ''};
export type TPage = number | '';
const limitPerRequest = 20;

export const AllTestsPage: React.FC = () => {
    const [inputValue, setInputValue] = useState<string>('');
    const [tableValue, setTablueValue] = useState<string>('');
    const [status, setStatus] = useState<Status[]>(options[0].value);
    const [page, setPage] = useState<TPage>(1);
    const [date, setDate] = useState<TDate>({dateStart: '', dateEnd: ''});
    const [sort, setSort] = useState<Sort>(Sort.UPDATED_ASC);
    const isInitialRender = useRef<boolean>(true);

    const email = tableValue || inputValue || undefined;

    const offsetValue = useMemo(() => {
        return page !== '' ? (page - 1) * limitPerRequest : 0;
    }, [page]);

    const dates = useMemo(() => {
        return {
            dateStart: new Date(date.dateStart).getTime() || undefined,
            dateEnd: new Date(date.dateEnd).getTime() || undefined,
        };
    }, [date]);

    const {
        data: tests = [],
        isLoading,
        refetch,
    } = useAllTestsByParams({
        limit: limitPerRequest,
        offset: offsetValue,
        status: status,
        email,
        sort: [sort],
        ...dates,
    });

    const selectEmail = useCallback((value: string) => {
        setTablueValue(value);
        setInputValue('');
    }, []);

    const selectHandler = useCallback((_, {value}) => {
        setStatus(value);
    }, []);

    const onChange = useCallback(
        (event: ChangeEvent<HTMLInputElement>) => {
            if (tableValue) setTablueValue('');
            setInputValue(event.target.value);
        },
        [tableValue],
    );

    const onChangePage = useCallback((nextPage: TPage) => {
        setPage(nextPage);
    }, []);

    const onDateChange = useCallback((event: ChangeEvent<HTMLInputElement>) => {
        setDate((prev) => ({...prev, [event.target.name]: event.target.value}));
    }, []);

    const dateClear = useCallback(() => {
        setDate({dateStart: '', dateEnd: ''});
    }, []);

    const onSubmit = () => {
        if (inputValue) refetch();
        setInputValue('');
    };

    const reset = useCallback(() => {
        setInputValue('');
        setTablueValue('');
        dateClear();
        setPage(1);
    }, [dateClear]);

    useEffect(() => {
        if (!isInitialRender.current) refetch();
    }, [status, tableValue, page, date, sort]);

    useEffect(() => {
        isInitialRender.current = false;
    }, []);

    return (
        <>
            <FiltersWrapper>
                <SearchInput
                    onChange={onChange}
                    onSubmit={onSubmit}
                    value={tableValue || inputValue}
                    placeholder='        Поиск по email'
                />
                <DatePicker date={date} dateHandler={onDateChange} clear={dateClear} />
                <Select options={options} value={status} onChange={selectHandler} />
            </FiltersWrapper>
            <ResultSection>
                Найдено: <ResultCount>{tests.length}</ResultCount> совпадений
                {!tests.length && <ShowAllResultsButton onClick={reset}>Показать все результаты</ShowAllResultsButton>}
            </ResultSection>
            <TestsTable isLoading={isLoading} tests={tests} selectEmail={selectEmail} sort={sort} setSort={setSort} />
            {!!tests.length && <Paginator onChangePage={onChangePage} currentPage={page} maxPage={10} />}
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
