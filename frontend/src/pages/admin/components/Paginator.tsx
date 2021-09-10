import React, {ChangeEvent} from 'react';
import styled from 'styled-components';

import {Icon} from '../../../components/Icon';

interface IProps {
    currentPage: number;
    onChangePage: (newPage: number | string) => void;
    maxPage: number;
}

const PaginatorMinWith = 24;
const paginatorWith = (page: number) => {
    const countSymblols = page.toString().length;
    return PaginatorMinWith + (countSymblols - 1) * 22;
};

const Paginator: React.FC<IProps> = ({currentPage, maxPage, onChangePage = () => null}) => {
    const onChange = (e: ChangeEvent<HTMLInputElement>) => {
        const value = Number(e.target.value) || '';
        if (onChangePage && value) onChangePage(value);
    };

    return (
        <PaginatorContainer>
            <PaginatorInput value={currentPage} onChange={onChange} style={{width: paginatorWith(currentPage)}} />
            <MaxPagesLabel>из {maxPage}</MaxPagesLabel>
            <PaginationButton disabled={currentPage === 1} onClick={() => onChangePage(-1)}>
                <Chevron name='chevron_right' />
            </PaginationButton>
            <PaginationButton disabled={currentPage >= maxPage} onClick={() => onChangePage(1)}>
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
