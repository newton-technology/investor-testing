import React from 'react';
import {Link} from 'react-router-dom';
import styled from 'styled-components';

import {Icon} from '../../../components/Icon';
import {ITest} from '../../category_test/CategoryTest';

export const TestCard: React.FC<ITest> = (props) => {
    const {
        id,
        status,
        category: {name, description},
    } = props;

    const isComplete = status === 'passed';

    return (
        <Container to={`tests/${id}`} isComplete={isComplete}>
            <Title>{description}</Title>
            {isComplete ? (
                <TestCompleteLabel>
                    <IconContainer name='check_circle' size={25} /> Тест пройден
                </TestCompleteLabel>
            ) : (
                <GoTestButton>Пройти →</GoTestButton>
            )}
        </Container>
    );
};

const Container = styled(Link)<{isComplete: boolean}>`
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-radius: 10px;
    color: inherit;
    padding: 24px;
    background-color: ${({theme, isComplete}) => (isComplete ? theme.palette.primary : theme.palette.bg.secondary)};
    color: ${({theme, isComplete}) => isComplete && theme.palette.bg.secondary};

    &:hover {
        opacity: 0.8;
    }
`;

const Title = styled.div`
    font-size: 20px;
    margin-bottom: 24px;
`;

const TestCompleteLabel = styled.div`
    color: ${({theme}) => theme.palette.bg.secondary};
    font-weight: 600;
    font-size: 20px;
    display: flex;
    justify-content: flex-end;
`;

const IconContainer = styled(Icon)`
    margin-right: 12px;
`;

const GoTestButton = styled.div`
    color: ${({theme}) => theme.palette.secondary};
    font-size: 18px;
    font-weight: 500;
`;
