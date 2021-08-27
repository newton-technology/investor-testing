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
            primary: palette.bg.primary || '#F2F6F8',
            secondary: palette.bg.secondary || '#FFF',
            footer: palette.bg.footer || '#1F1B30',
        },
    },
};
