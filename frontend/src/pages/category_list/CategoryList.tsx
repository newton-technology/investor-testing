import React from 'react';
import styled from 'styled-components';

import {useQuery} from '../../hooks/useQuery';
import {CategoryListApi} from '../../api/CategoryListApi';
import {ITest} from '../category_test/CategoryTest';
import {Loader} from '../../components/Loader';
import {ServerErrorMessage} from '../../components/ServerErrorMessage';
import {TestCard} from './components/TestCard';

export const CategoryList: React.FC = () => {
    const {data: tests = [], isLoading, isError} = useQuery(CategoryListApi.getCategories);

    if (isError) {
        return <ServerErrorMessage />;
    }

    if (isLoading) {
        return <Loader />;
    }

    return (
        <Container>
            <Title>Выбери категорию теста</Title>
            {isLoading ? (
                <Loader />
            ) : (
                <TestsList>
                    {tests.map((test: ITest) => {
                        return <TestCard key={test.category.id} status={test.status} {...test.category} />;
                    })}
                </TestsList>
            )}
        </Container>
    );
};

const Container = styled.div``;

const Title = styled.div`
    font-weight: 700;
    font-size: 32px;
    margin-bottom: 32px;
`;

const TestsList = styled.div`
    display: grid;
    grid-gap: 20px;
    grid-template-columns: repeat(2, 1fr);
`;
