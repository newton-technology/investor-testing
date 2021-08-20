import React from 'react';
import styled from 'styled-components';

import {Icon} from '../../../components/Icon';

export const InfoIcon: React.FC = (props) => {
    return (
        <InfoIconContainer>
            <Icon name='info' />
            <Tooltip>{props.children}</Tooltip>
        </InfoIconContainer>
    );
};

const Tooltip = styled.div`
    display: none;
    font-weight: 400;
    position: absolute;
    font-size: 16px;
    padding: 16px;
    background-color: ${({theme}) => theme.palette.regular};
    color: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 10px;
    top: calc(100% + 12px);
    left: 50%;
    transform: translateX(-50%);
    width: 300px;
    z-index: 100;

    &:before {
        content: '';
        width: 0;
        display: block;
        height: 0;
        border: 10px solid transparent;
        border-bottom-color: ${({theme}) => theme.palette.regular};
        position: absolute;
        top: -20px;
        left: 50%;
        transform: translateX(-50%);
    }
`;

const InfoIconContainer = styled.span`
    position: relative;
    cursor: pointer;
    color: ${({theme}) => theme.palette.secondary};

    &:hover {
        color: ${({theme}) => theme.palette.regular};

        ${Tooltip} {
            display: block;
        }
    }
`;
