import React from 'react';
import styled from 'styled-components';

import {CategoryListApi} from '../../api/CategoryListApi';
import {IResponseError} from '../../api/CategoryTestApi';
import {ErrorMessage} from '../../components/ErrorMessage';
import {Loader} from '../../components/Loader';
import {useQuery} from '../../hooks/useQuery';
import {useScrollToTop} from '../../hooks/useScrollToTop';
import {CategoryCard} from './components/CategoryCard';
import MOEXMobileBanner from '../category_test/components/MoExBanner';
import MOEXDesktopBanner from './components/MoExBanner';

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
                <DesktopBanner />
                <MobileBanner />
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

const DesktopBanner = styled(MOEXDesktopBanner)`
    visibility: hidden;
    position: fixed;

    ${({theme}) => theme.breakpoint('lg')`
        visibility: visible;
        position: relative;
    `}
`;

const MobileBanner = styled(MOEXMobileBanner)`
    padding: 24px 24px 213px;
    margin: 12px 0;
    text-align: center;

    ${({theme}) => theme.breakpoint('lg')`
        visibility: hidden;
        position: fixed;
    `}

    svg#moex-background {
        position: absolute;
        bottom: 0;
        right: 0;
        transform: translate(84px, 42px);
        width: 338px;
        height: 263px;

        [data-hidden-mb] {
            display: none;
        }
    }
`;
