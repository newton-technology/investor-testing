import React, {memo, MouseEvent} from 'react';
import {useHistory} from 'react-router';
import styled from 'styled-components';

import {ITestResponse, Sort, Status} from '../../../../api/ManagmentApi';
import {Icon} from '../../../../components/Icon';
import {Loader} from '../../../../components/Loader';
import {dateFormatter} from '../../../../utils/tableUtils';
import NoReslt from './components/NoReslt';

interface IProps {
    tests: ITestResponse[];
    isLoading?: boolean;
    selectEmail: (email: string) => void;
    sort: Sort;
    setSort: (value: Sort) => void;
}

interface ITableColumn {
    title: string;
    value: string;
    sortable?: boolean;
}

const columns: ITableColumn[] = [
    {title: 'Дата', value: 'updatedAt', sortable: true},
    {title: 'Email', value: 'userEmail'},
    {title: 'Название теста', value: 'name'},
    {title: 'Результат', value: 'status'},
];

const TestsTable: React.FC<IProps> = ({tests, sort, setSort, isLoading, selectEmail}) => {
    const {push} = useHistory();

    if (!isLoading && !tests.length) {
        return <NoReslt />;
    }

    const changeSortDirection = () => {
        setSort(sort === Sort.UPDATED_ASC ? Sort.UPDATED_DESC : Sort.UPDATED_ASC);
    };

    const selectTest = (id: number) => {
        push(`/test/${id}`);
    };

    const emailHandler = (email: string) => (event: MouseEvent<HTMLElement>) => {
        event.stopPropagation();
        selectEmail(email);
    };

    const isDesc = sort === Sort.UPDATED_DESC;

    return (
        <Table>
            <TableHead>
                <TableRow>
                    {columns.map((column) => (
                        <TH key={column.value}>
                            <HeadContent
                                pointer={column.sortable}
                                onClick={column.sortable ? changeSortDirection : undefined}>
                                <HeaderTitle sortable={column?.sortable}>{column.title}</HeaderTitle>
                                {column.sortable && <SortChevron size={24} isDesc={isDesc} name='chevron_right' />}
                            </HeadContent>
                        </TH>
                    ))}
                </TableRow>
            </TableHead>
            <TableBody>
                {isLoading ? (
                    <LoaderContainer>
                        <td>
                            <Loader />
                        </td>
                    </LoaderContainer>
                ) : (
                    tests.map((test) => {
                        const isPassed = test.status === Status.PASSED;
                        return (
                            <TableRow key={test.id} onClick={() => selectTest(test.id)}>
                                <TD>{dateFormatter(test.updatedAt, 'D MMMM в H:m')}</TD>
                                <TD onClick={emailHandler(test.userEmail)}>
                                    <BodyContent pointer>{test.userEmail}</BodyContent>
                                </TD>
                                <TD>
                                    <BodyContent>{test.category.description}</BodyContent>
                                </TD>
                                <TD>
                                    <TDContent>
                                        <Icon name={isPassed ? 'test_passed' : 'test_failed'} size={25} />
                                    </TDContent>
                                </TD>
                            </TableRow>
                        );
                    })
                )}
            </TableBody>
        </Table>
    );
};

const Table = styled.table`
    border: 0;
    border-spacing: 0 4px;
    margin-bottom: 48px;
    width: 100%;
`;

const TH = styled.th`
    background-color: ${({theme}) => theme.palette.bg.secondary};
    font-size: 20px;
    line-height: 26px;
    padding-bottom: 24px;
    padding-top: 24px;
    text-align: left;

    &:first-child {
        border-radius: 10px 0px 0px 10px;
        padding-left: 32px;
    }

    &: last-child {
        border-radius: 0px 10px 10px 0px;
        padding-right: 32px;
        width: 0px;
    }
`;

const TD = styled.td`
    background-color: ${({theme}) => theme.palette.bg.secondary};
    padding-bottom: 17px;
    padding-top: 15px;
    vertical-align: top;
    width: max-content;

    &:first-child {
        border-radius: 10px 0px 0px 10px;
        padding-left: 32px;
    }

    &: last-child {
        border-radius: 0px 10px 10px 0px;
        padding-right: 32px;
    }
`;

const TDContent = styled.div`
    align-items: flex-end;
    display: flex;
    flex-direction: column;
    height: 42px;
    justify-content: center;
`;

const HeadContent = styled.div<{pointer?: boolean}>`
    align-items: center;
    cursor: ${({pointer}) => (pointer ? 'pointer' : 'initial')};
    display: flex;
`;

const SortChevron = styled(Icon)<{isDesc: boolean}>`
    transform: rotate(${({isDesc}) => (isDesc ? '90' : '-90')}deg);

    & path {
        fill: ${({theme}) => theme.palette.regular};
    }
`;

const HeaderTitle = styled.div<{sortable?: boolean}>`
    margin-right: ${({sortable}) => (sortable ? 16 : 0)}px;
`;

const BodyContent = styled.div<{pointer?: boolean}>`
    color: ${({theme, pointer}) => (pointer ? theme.palette.primary : theme.palette.regular)};
    cursor: ${({pointer}) => (pointer ? 'pointer' : 'initial')};
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    height: 42px;
    max-width: 374px;
    overflow: hidden;
`;

const TableRow = styled.tr`
    cursor: pointer;

    & > * {
        cursor: pointer;
    }
`;

const TableHead = styled.thead`
    border-bottom: 4px transparent;
`;
const LoaderContainer = styled(TableRow)`
    height: 102px;
    position: relative;

    & span {
        position: absolute;
        left: 50%;
        top: 0px;
    }
`;

const TableBody = styled.tbody``;

export default memo(TestsTable);
