import React, {useState, useCallback, ChangeEvent} from 'react';
import styled from 'styled-components';

import DatePicker from './components/DatePicker';
import SearchInput from './components/SearchInput';
import Select from './components/Select';

const options = [
    {title: 'Все тесты', value: null},
    {title: 'Только успешные', value: ''},
];

export const AllTestsPage: React.FC = () => {
    const [email, setEmail] = useState<string>('');
    const [status, setStatus] = useState(options[0].value);

    const searchHanlder = useCallback((e: ChangeEvent<HTMLInputElement>) => {
        setEmail(e.target.value);
    }, []);

    const onSubmit = useCallback(() => {
        setEmail('');
    }, []);

    const selectHandler = useCallback((e, {value}) => {
        setStatus(value);
    }, []);

    return (
        <FiltersWrapper>
            <SearchInput onChange={searchHanlder} onSubmit={onSubmit} value={email} placeholder='Поиск по email' />
            <DatePicker />
            <Select options={options} value={status} onChange={selectHandler} />
        </FiltersWrapper>
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
