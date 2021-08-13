import {createGlobalStyle} from 'styled-components';
import {normalize} from './styled-normalize';
import {reset} from './styled-reset';

export const GlobalStyle = createGlobalStyle`
    ${reset}
    ${normalize}

    body {
        font-family: 'IBM Plex Sans', sans-serif;
        font-size: 16px;
        color: ${({theme}) => theme.palette.regular};
        background-color: ${({theme}) => theme.palette.mainBg};
        line-height: 1.3;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
`;
