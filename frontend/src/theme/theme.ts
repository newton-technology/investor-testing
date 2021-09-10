import {DefaultTheme} from 'styled-components';

import customize from '../customize.json';

const palette = customize.palette;

export const theme: DefaultTheme = {
    palette: {
        regular: palette.regular || '#3A3463',
        primary: palette.primary || '#0DAAEC',
        secondary: palette.secondary || '#2F6FEB',
        error: '#E30B17',
        bg: {
            primary: '#F2F6F8',
            secondary: '#FFF',
            footer: '#1F1B30',
            darkBlue: '#3A3463',
            authorization: 'linear-gradient(139.02deg, #65dfe7 -19.37%, #587cfc 116.76%)',
        },
        border: {
            input: '#C4C8DB',
            primary: palette.bg.primary || '#F2F6F8',
            secondary: palette.bg.secondary || '#FFF',
            footer: palette.bg.footer || '#1F1B30',
            authorization: 'linear-gradient(139.02deg, #65dfe7 -19.37%, #587cfc 116.76%)',
        },
    },
    breakpoints: {
        sm: 375,
        md: 768,
        lg: 1200,
    },
    breakpoint: (size) => {
        switch (size) {
            case 'sm':
                return (styles) => `@media (min-width: ${theme.breakpoints.sm}px) {${styles}}`;
            case 'md':
                return (styles) => `@media (min-width: ${theme.breakpoints.md}px) {${styles}}`;
            case 'lg':
                return (styles) => `@media (min-width: ${theme.breakpoints.lg}px) {${styles}}`;
        }
    },
};
