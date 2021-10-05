import React, {memo} from 'react';
import styled from 'styled-components';

interface IProps {
    text: string;
    filter: string;
}

const HightLightText: React.FC<IProps> = ({text, filter}) => {
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
                                <HiLight>{foundString}</HiLight>
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

const HiLight = styled.span`
    background-color: #dbe9ff;
`;

// background-color: ${({theme}) => theme.palette.warning};

export default memo(HightLightText);
