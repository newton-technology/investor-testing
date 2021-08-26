import React from 'react';
import styled from 'styled-components';

import {useQuery} from '../../hooks/useQuery';
import {CategoryListApi} from '../../api/CategoryListApi';
import {Loader} from '../../components/Loader';
import {ServerErrorMessage} from '../../components/ServerErrorMessage';
import {CategoryCard} from './components/CategoryCard';
import {IResponseError} from '../../api/CategoryTestApi';
import {breakpoint} from '../../theme/breakpont';

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

    if (isLoading) {
        return <Loader />;
    }

    if (isError) {
        return <ServerErrorMessage />;
    }

    return (
        <Container>
            <Title>Выбери категорию теста</Title>
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

    ${breakpoint('md')`
        font-size: 32px;
    `}
`;

const List = styled.div`
    display: grid;
    grid-gap: 20px;

    ${breakpoint('md')`
        grid-template-columns: repeat(2, 1fr);
    `}
`;
