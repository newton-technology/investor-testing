import {createGlobalStyle} from 'styled-components';

import {normalize} from './styled-normalize';
import {reset} from './styled-reset';

export const GlobalStyle = createGlobalStyle`
    ${reset}
    ${normalize}

    body {
        font-family: 'IBM Plex Sans', sans-serif;
        font-size: 17px;
        color: ${({theme}) => theme.palette.regular};
        background-color: ${({theme}) => theme.palette.bg.primary};
        line-height: 1.3;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    @keyframes fade {
        0% {opacity: 0}
        100% {opacity:1}
    }

    @keyframes fade-leave {
        0% {opacity:1}
        100% {opacity: 0}
    }

    .rc-tooltip {
        font-size: 16px;
        border-radius: 10px;
        color: ${({theme}) => theme.palette.bg.secondary};
        background-color: ${({theme}) => theme.palette.regular};
        padding: 16px;
        max-width: 430px;
        
        &.fade {
            animation: fade .3s forwards;
        }

        &.fade-leave {
            animation: fade-leave .2s forwards;
        }

        &-hidden {
            display: none;
        }

        &-arrow {
            position: absolute;
            width: 0;
            height: 0;
            border-color: transparent;
            border-style: solid;
            display: none;
            
            ${({theme}) => theme.breakpoint('md')`
                display: block;
            `}
        }

        &-placement {
            &-bottom .rc-tooltip-arrow {
                left: 50%;
            }
            
            &-bottom, &-bottomLeft, &-bottomRight {
                .rc-tooltip-arrow {
                    top: -5px;
                    margin-left: -6px;
                    border-width: 0 6px 6px;
                    border-bottom-color: ${({theme}) => theme.palette.regular};
                }
            }
        }
    }
`;
