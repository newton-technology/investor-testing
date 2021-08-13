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
            <Title>Тестирование</Title>
            <Subtitle>
                Согласно законодательству, доступ к рисковым сделкам и финансовым инструментам могут получить только
                инвесторы, прошедшие проверку. Пройдите тестирование, чтобы расширить перечень доступных сделок и ценных
                бумаг.
            </Subtitle>
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

const Container = styled.div``;

const Title = styled.div``;

const Subtitle = styled.div`
    margin-bottom: 40px;
`;

const TestsList = styled.div``;
