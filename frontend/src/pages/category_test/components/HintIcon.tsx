import Tooltip from 'rc-tooltip';
import React from 'react';
import styled from 'styled-components';

import {Icon} from '../../../components/Icon';

export const HintIcon: React.FC = (props) => {
    return (
        <Tooltip overlay={props.children} placement='bottom' align={{offset: [0, 6]}} transitionName={'fade'}>
            <HintIconContainer>
                <Icon name='info' />
            </HintIconContainer>
        </Tooltip>
    );
};

const HintIconContainer = styled.span`
    position: relative;
    cursor: pointer;
    color: ${({theme}) => theme.palette.secondary};

    &:hover {
        color: ${({theme}) => theme.palette.regular};
    }
`;
