import React from 'react';
import styled from 'styled-components';

import {useQuery} from '../../hooks/useQuery';
import {CategoryListApi} from '../../api/CategoryListApi';
import {ITest} from '../category_test/CategoryTest';
import {Loader} from '../../components/Loader';
import {ServerErrorMessage} from '../../components/ServerErrorMessage';
import {TestCard} from './components/TestCard';
import {IResponseError} from '../../api/CategoryTestApi';

export const CategoryList: React.FC = () => {
    const {data, isLoading, isError} = useQuery<ITest[], IResponseError>(CategoryListApi.getCategories);

    if (isLoading) {
        return <Loader />;
    }

    if (isError) {
        return <ServerErrorMessage />;
    }

    return (
        <Container>
            <Title>Выбери категорию теста</Title>
            <TestsList>
                {data?.map((test: ITest) => {
                    return <TestCard key={test.category.id} status={test.status} {...test.category} />;
                })}
            </TestsList>
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
