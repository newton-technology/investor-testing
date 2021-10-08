import Tooltip from 'rc-tooltip';
import React from 'react';
import styled from 'styled-components';

interface IProps {
    text: string;
}

export const HintedText: React.FC<IProps> = ({text, children}) => {
    return (
        <Tooltip overlay={text} placement='bottom' align={{offset: [0, 5]}} transitionName={'fade'}>
            <HintContainer>{children}</HintContainer>
        </Tooltip>
    );
};

const HintContainer = styled.span`
    position: relative;
    cursor: pointer;
    color: ${({theme}) => theme.palette.secondary};
    margin: 0;

    &:hover {
        color: ${({theme}) => theme.palette.error};
    }
`;
