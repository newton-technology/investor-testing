import parse, {HTMLReactParserOptions, domToReact} from 'html-react-parser';
import Tooltip from 'rc-tooltip';
import React from 'react';
import styled from 'styled-components';

import {Icon} from '../../../components/Icon';

interface IProps {
    text: string;
}

export const HintedText: React.FC<IProps> = ({text}) => {
    const options: HTMLReactParserOptions = {
        replace: (domNode: any) => {
            if (domNode.type === 'tag' && domNode.name === 'hint') {
                return (
                    <Tooltip
                        overlay={domToReact(domNode.children, options)}
                        placement='bottom'
                        align={{offset: [0, 6]}}
                        transitionName={'fade'}>
                        <HintIconContainer>
                            <Icon name='info' />
                        </HintIconContainer>
                    </Tooltip>
                );
            }
        },
    };
    return <>{parse(text, options)}</>;
};

const HintIconContainer = styled.span`
    position: relative;
    cursor: pointer;
    color: ${({theme}) => theme.palette.secondary};
    margin: 0 4px;
    top: -2px;

    &:hover {
        color: ${({theme}) => theme.palette.regular};
    }
`;
