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
            <svg viewBox='0 0 79 79' xmlns='http://www.w3.org/2000/svg'>
                <path d='M48.2919 8.10005C31.221 3.52592 13.6742 13.6566 9.10005 30.7275C4.52591 47.7984 14.6566 65.3452 31.7275 69.9193C48.7984 74.4934 66.3452 64.3628 70.9193 47.2919L77.6808 49.1036C72.1061 69.9088 50.7209 82.2555 29.9157 76.6808C9.11056 71.1061 -3.23616 49.7209 2.33857 28.9157C7.9133 8.11056 29.2984 -4.23616 50.1036 1.33857C55.373 2.7505 60.1076 5.18082 64.1453 8.36364L59.8119 13.861C56.5065 11.2555 52.6268 9.2616 48.2919 8.10005Z' />
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
    height: ${isFullScreen ? '100vh' : '100px'};
`}

    svg {
        animation: ${rotate} 1s cubic-bezier(0.17, 0.67, 0.83, 0.67) infinite;
        fill: ${({theme}) => theme.palette.secondary};
        height: ${({isFullScreen}) => (isFullScreen ? '79px' : '26px')};
        width: ${({isFullScreen}) => (isFullScreen ? '79px' : '26px')};
    }
`;
