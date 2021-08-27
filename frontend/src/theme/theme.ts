import {DefaultTheme} from 'styled-components';

export const theme: DefaultTheme = {
    palette: {
        regular: '#3A3463',
        primary: '#0DAAEC',
        secondary: '#2F6FEB',
        error: '#E30B17',
        bg: {
            primary: '#F2F6F8',
            secondary: '#FFF',
            footer: '#1F1B30',
            authorization: 'linear-gradient(139.02deg, #65dfe7 -19.37%, #587cfc 116.76%)',
        },
    },
    breakpoints: {
        xs: 0,
        sm: 576,
        md: 768,
        lg: 1200,
    },
    breakepoint: (size) => {
        switch (size) {
            case 'xs':
                return (styles) => `@media (max-width: ${theme.breakpoints.sm}px) {${styles}}`;
            case 'sm':
                return (styles) => `@media (max-width: ${theme.breakpoints.md}px) {${styles}}`;
            case 'md':
                return (styles) => `@media (max-width: ${theme.breakpoints.lg}px) {${styles}}`;
            case 'lg':
                return (styles) => `@media (min-width: ${theme.breakpoints.lg}px) {${styles}}`;
        }
    },
};
