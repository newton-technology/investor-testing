import React from 'react';
import styled from 'styled-components';

import {Loader} from './Loader';

interface IProps {
    className?: string;
    isPlain?: boolean;
    isLoading?: boolean;
    onClick?: () => void;
}

export const Button: React.FC<IProps> = (props) => {
    const {className, children, isPlain = false, onClick, isLoading} = props;
    return (
        <ButtonContainer onClick={onClick} isPlain={isPlain} className={className}>
            {children}
            {isLoading && <LoaderIcon isFull={false} />}
        </ButtonContainer>
    );
};

const ButtonContainer = styled.button<{isPlain: boolean}>`
    background-color: ${({theme, isPlain}) => (isPlain ? 'transparent' : theme.palette.secondary)};
    color: ${({theme, isPlain}) => (isPlain ? theme.palette.secondary : '#fff')};
    border: 1px solid ${({theme}) => theme.palette.secondary};
    height: 53px;
    font-size: 18px;
    font-weight: 600;
    padding: 0 34px;
    display: inline-flex;
    align-items: center;
    border-radius: 4px;

    &:hover {
        opacity: 0.9;
    }

    svg {
        fill: ${({theme, isPlain}) => (isPlain ? theme.palette.secondary : '#fff')};
    }
`;

const LoaderIcon = styled(Loader)`
    margin-left: 10px;
`;
