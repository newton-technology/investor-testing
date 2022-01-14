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
                    <>
                        &nbsp;
                        <Tooltip
                            overlay={domToReact(domNode.children, options)}
                            placement='bottom'
                            trigger={['click']}
                            align={{offset: [0, 6]}}
                            transitionName={'fade'}>
                            <HintIconContainer>
                                <NonBreakingIcon name='info' />
                            </HintIconContainer>
                        </Tooltip>
                    </>
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
    margin-right: 4px;
    padding-right: 17px;

    &:hover {
        color: ${({theme}) => theme.palette.regular};
    }
`;

const NonBreakingIcon = styled(Icon)`
    position: absolute;
    top: 50%;
    left: 0;
    transform: translateY(-50%);
`;
