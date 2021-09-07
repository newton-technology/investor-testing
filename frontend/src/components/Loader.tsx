import React from 'react';
import styled, {keyframes} from 'styled-components';

/*eslint max-len: ["error", { "code": 1000 }]*/

interface IProps {
    isInline?: boolean;
    className?: string;
    isFullScreen?: boolean;
}

export const Loader: React.FC<IProps> = ({isInline, className, isFullScreen}) => {
    return (
        <LoaderContainer isInline={isInline} isFullScreen={isFullScreen} className={className}>
            <svg viewBox='0 0 80 80' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path d='M49.2919 9.10005C32.221 4.52592 14.6742 14.6566 10.1001 31.7275C5.52591 48.7984 15.6566 66.3452 32.7275 70.9193C49.7984 75.4934 67.3452 65.3628 71.9193 48.2919L78.6808 50.1036C73.1061 70.9088 51.7209 83.2555 30.9157 77.6808C10.1106 72.1061 -2.23616 50.7209 3.33857 29.9157C8.9133 9.11056 30.2984 -3.23616 51.1036 2.33857C56.373 3.7505 61.1076 6.18082 65.1453 9.36364L60.8119 14.861C57.5065 12.2555 53.6268 10.2616 49.2919 9.10005Z' />
            </svg>
        </LoaderContainer>
    );
};

const rotate = keyframes`
    from {
        transform: rotate(0deg);
    }
    
    to {
        transform: rotate(360deg);
    }
`;

const LoaderContainer = styled.span<{isInline?: boolean; isFullScreen?: boolean}>`
    display: ${({isInline}) => (isInline ? 'inline-flex' : 'flex')};
    ${({isFullScreen, isInline}) =>
        (isFullScreen || !isInline) &&
        `
    align-items: center;
    justify-content: center;
    height: ${isFullScreen ? '100vh' : '500px'};
`}

    svg {
        animation: ${rotate} 1s cubic-bezier(0.17, 0.67, 0.83, 0.67) infinite;
        fill: ${({theme}) => theme.palette.secondary};
        height: ${({isFullScreen}) => (isFullScreen ? '80px' : '26px')};
        width: ${({isFullScreen}) => (isFullScreen ? '80px' : '26px')};
    }
`;
