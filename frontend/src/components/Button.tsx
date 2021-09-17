import React from 'react';
import {Link} from 'react-router-dom';
import styled, {css} from 'styled-components';

import {Loader} from './Loader';

interface IProps {
    className?: string;
    isPlain?: boolean;
    isLoading?: boolean;
    disabled?: boolean;
    to?: string;
    onClick?: () => void;
}

export const Button: React.FC<IProps> = (props) => {
    const {className, children, isPlain = false, onClick, isLoading, disabled = false, to} = props;

    if (to) {
        return (
            <LinkContainer to={to} $isPlain={isPlain} className={className}>
                {children}
            </LinkContainer>
        );
    }
    return (
        <ButtonContainer onClick={onClick} $isPlain={isPlain} disabled={disabled || isLoading} className={className}>
            {children}
            {isLoading && <LoaderIcon isInline={true} />}
        </ButtonContainer>
    );
};

const button = css<{$isPlain: boolean}>`
    background-color: ${({theme, $isPlain}) => ($isPlain ? 'transparent' : theme.palette.secondary)};
    color: ${({theme, $isPlain}) => ($isPlain ? theme.palette.secondary : theme.palette.bg.secondary)};
    border: 1px solid ${({theme}) => theme.palette.secondary};
    height: 53px;
    font-size: 18px;
    font-weight: 600;
    padding: 0 34px;
    display: inline-flex;
    align-items: center;
    border-radius: 4px;
    justify-content: center;
    transition: transform 0.2s linear;

    &:not(:disabled):hover {
        transform: translateY(-3px);
    }
`;

const ButtonContainer = styled.button<{$isPlain: boolean}>`
    ${button};

    &:disabled {
        cursor: not-allowed;
        opacity: 0.4;
    }

    svg {
        fill: ${({theme, $isPlain}) => ($isPlain ? theme.palette.secondary : '#fff')};
    }
`;

const LinkContainer = styled(Link)<{$isPlain: boolean}>`
    ${button};
`;

const LoaderIcon = styled(Loader)`
    margin-left: 10px;
`;
