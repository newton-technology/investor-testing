import React from 'react';
import {Link} from 'react-router-dom';
import styled from 'styled-components';
import {ITest} from '../../category_test/CategoryTest';

export const TestCard: React.FC<ITest> = (props) => {
    const {
        id,
        status,
        category: {name, description},
    } = props;

    const isComplete = status === 'passed';

    return (
        <Container to={`tests/${id}`}>
            <Date>{description}</Date>
            {isComplete ? 'пройден' : 'не пройден'}
            <Title>{name}</Title>
        </Container>
    );
};

const Container = styled(Link)`
    display: block;
    color: inherit;

    & + & {
        margin-top: 30px;
    }
`;

const Date = styled.div``;

const Title = styled.div``;
