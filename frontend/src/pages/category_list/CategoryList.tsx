import React from 'react';
import styled from 'styled-components';

import {TestCard} from './components/TestCard';
import {useQuery} from '../../hooks/useQuery';
import {CategoryListApi} from '../../api/CategoryListApi';
import {ITest} from '../category_test/CategoryTest';
import {Loader} from '../../components/Loader';

export const CategoryList: React.FC = () => {
    const {data: tests = [], isLoading, isError} = useQuery(CategoryListApi.getTests);

    if (isError) {
        return <div>Ошибка</div>;
    }

    return (
        <Container>
            <Title>Выбери категорию теста</Title>
            {isLoading ? (
                <Loader />
            ) : (
                <TestsList>
                    {tests.map((test: ITest) => {
                        return <TestCard key={test.id} {...test} />;
                    })}
                </TestsList>
            )}
        </Container>
    );
};

const Container = styled.div`
    margin-top: 20px;
`;

const Title = styled.div`
    font-weight: 500;
    font-size: 32px;
    margin-bottom: 45px;
`;

const TestsList = styled.div`
    display: grid;
    grid-gap: 20px;
    grid-template-columns: repeat(3, 1fr);
`;
