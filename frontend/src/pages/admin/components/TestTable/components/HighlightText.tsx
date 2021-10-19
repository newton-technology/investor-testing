import React, {memo} from 'react';
import styled from 'styled-components';

interface IProps {
    text: string;
    filter: string;
}

const HighlightText: React.FC<IProps> = ({text, filter}) => {
    if (!filter) return <>{text}</>;
    const regexp = new RegExp(filter, 'ig');
    const matchValue = text.match(regexp);
    if (matchValue) {
        return (
            <>
                {text.split(regexp).map((str, index, array) => {
                    if (index < array.length - 1) {
                        const foundString = matchValue.shift();
                        return (
                            <React.Fragment key={`${index}-${str}`}>
                                {str}
                                <Highlight>{foundString}</Highlight>
                            </React.Fragment>
                        );
                    }
                    return <React.Fragment key={`${index}-${str}`}>{str}</React.Fragment>;
                })}
            </>
        );
    }
    return <>{text}</>;
};

const Highlight = styled.span`
    background-color: ${({theme}) => theme.palette.bg.lightBlue};
`;

export default memo(HighlightText);
