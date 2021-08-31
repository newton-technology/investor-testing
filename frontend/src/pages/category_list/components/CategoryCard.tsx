import React from 'react';
import {Link} from 'react-router-dom';
import styled, {css} from 'styled-components';

import {Icon} from '../../../components/Icon';
import {breakpoint} from '../../../theme/breakpont';

interface IProps {
    id: number;
    descriptionShort: string;
    status: string | null;
}

export const CategoryCard: React.FC<IProps> = (props) => {
    const {status, id, descriptionShort} = props;

    const isComplete = status === 'passed';

    return isComplete ? (
        <Container>
            <Title>{descriptionShort}</Title>
            <CompleteLabel>
                <IconContainer name='check_circle' size={25} /> Тест пройден
            </CompleteLabel>
        </Container>
    ) : (
        <ContainerLink to={`tests/${id}`}>
            <Title>{descriptionShort}</Title>
            <GoTestButton>Пройти →</GoTestButton>
        </ContainerLink>
    );
};

const containerCss = css`
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-radius: 10px;
    padding: 24px;
`;

const Container = styled.div`
    ${containerCss};
    background-color: ${({theme}) => theme.palette.primary};
    color: ${({theme}) => theme.palette.bg.secondary};
`;

const ContainerLink = styled(Link)`
    ${containerCss};
    background-color: ${({theme}) => theme.palette.bg.secondary};
    color: inherit;
    transition: transform 0.3s ease-in-out;
    backface-visibility: hidden;

    &:hover {
        transform: translateY(-5px);
    }
`;

const Title = styled.div`
    margin-bottom: 24px;

    ${breakpoint('md')`
        font-size: 20px;
    `}
`;

const CompleteLabel = styled.div`
    color: ${({theme}) => theme.palette.bg.secondary};
    font-weight: 600;
    display: flex;
    justify-content: flex-end;

    ${breakpoint('md')`
        font-size: 20px;
    `}
`;

const IconContainer = styled(Icon)`
    margin-right: 12px;
`;

const GoTestButton = styled.div`
    color: ${({theme}) => theme.palette.secondary};
    font-size: 18px;
    font-weight: 500;
`;
