import React from 'react';
import styled from 'styled-components';

import {CategoryListApi} from '../../api/CategoryListApi';
import {IResponseError} from '../../api/CategoryTestApi';
import {ErrorMessage} from '../../components/ErrorMessage';
import {Loader} from '../../components/Loader';
import {useQuery} from '../../hooks/useQuery';
import {useScrollToTop} from '../../hooks/useScrollToTop';
import {CategoryCard} from './components/CategoryCard';

export interface ICategory {
    category: {
        id: number;
        description: string;
        descriptionShort: string;
    };
    status: string | null;
}

export const CategoryList: React.FC = () => {
    const {data, isLoading, isError} = useQuery<ICategory[], IResponseError>(CategoryListApi.getCategories);
    useScrollToTop();

    if (isLoading) {
        return <Loader />;
    }

    if (isError) {
        return <ErrorMessage />;
    }

    return (
        <Container>
            <Title>Выберите категорию теста</Title>
            <List>
                {data?.map((test: ICategory) => {
                    return <CategoryCard key={test.category.id} status={test.status} {...test.category} />;
                })}
            </List>
        </Container>
    );
};

const Container = styled.div``;

const Title = styled.div`
    font-weight: 700;
    font-size: 28px;
    margin-bottom: 32px;

    ${({theme}) => theme.breakpoint('md')`
        font-size: 32px;
    `}
`;

const List = styled.div`
    display: grid;
    grid-gap: 20px;

    ${({theme}) => theme.breakpoint('md')`
        grid-template-columns: repeat(2, 1fr);
    `}
`;
