import React from 'react';
import styled from 'styled-components';

interface IProps {
    className?: string;
    isPlain?: boolean;
    onClick?: () => void;
}

export const Button: React.FC<IProps> = (props) => {
    const {className, children, isPlain = false, onClick} = props;
    return (
        <ButtonContainer onClick={onClick} isPlain={isPlain} className={className}>
            {children}
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
`;
