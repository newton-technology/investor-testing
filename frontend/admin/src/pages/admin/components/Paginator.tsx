import React, {ChangeEvent, FormEvent, useEffect, useState} from 'react';
import styled from 'styled-components';

import {Icon} from '../../../components/Icon';
import {TPage} from '../AllTestsPage';

interface IProps {
    currentPage: TPage;
    onChangePage: (newPage: TPage) => void;
    maxPage: number;
}

const PaginatorMinWith = 24;
const paginatorWith = (page: number) => {
    const countSymblols = page.toString().length;
    return PaginatorMinWith + (countSymblols - 1) * 22;
};

const Paginator: React.FC<IProps> = ({currentPage, maxPage, onChangePage = () => null}) => {
    const [localPageState, setLocalPageState] = useState<TPage>(currentPage);
    const pageValue = typeof localPageState === 'number' ? localPageState : 0;

    const onChangeHandler = (e: ChangeEvent<HTMLInputElement>) => {
        const value = Number(e.target.value) || '';
        if (value <= maxPage) setLocalPageState(value);
    };
    const onSubmitHandler = (e: FormEvent) => {
        e.preventDefault();
        if (onChangePage) onChangePage(localPageState);
    };

    useEffect(() => {
        setLocalPageState(currentPage);
    }, [currentPage]);

    return (
        <PaginatorContainer>
            <form action='' onSubmit={onSubmitHandler}>
                <PaginatorInput
                    value={localPageState}
                    onChange={onChangeHandler}
                    style={{width: paginatorWith(currentPage || 0)}}
                />
            </form>
            <MaxPagesLabel>из {maxPage}</MaxPagesLabel>
            <PaginationButton
                disabled={currentPage === 1 || currentPage === ''}
                onClick={() => onChangePage(pageValue - 1)}>
                <Chevron name='chevron_right' />
            </PaginationButton>
            <PaginationButton disabled={currentPage >= maxPage} onClick={() => onChangePage(pageValue + 1)}>
                <Chevron name='chevron_right' />
            </PaginationButton>
        </PaginatorContainer>
    );
};

const PaginatorContainer = styled.div`
    display: flex;
    margin-left: auto;
    width: max-content;
`;

const PaginatorInput = styled.input`
    background: transparent;
    border: 1px solid #c4c8db;
    font-size: 14px;
    font-weight: normal;
    line-height: 130%;

    max-width: 88px;
    text-align: center;

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    -moz-appearance: textfield;
`;

const MaxPagesLabel = styled.div`
    align-items: center;
    color: ${({theme}) => theme.palette.regular};
    display: flex;
    font-size: 14px;
    font-weight: normal;
    line-height: 130%;
    margin: 0px 8px;
`;

const PaginationButton = styled.button`
    align-items: center;
    background-color: ${({theme}) => theme.palette.secondary};
    cursor: pointer;
    display: flex;
    flex-direction: column;
    height: 24px;
    justify-content: center;
    width: 24px;

    &:nth-child(3) span {
        transform: rotate(180deg);
    }

    &:disabled {
        background-color: #c4c8db;
    }
`;

const Chevron = styled(Icon)`
    & path {
        fill: #fff;
    }
`;

export default Paginator;
