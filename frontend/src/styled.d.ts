import 'styled-components';

declare module 'styled-components' {
    export interface DefaultTheme {
        palette: {
            regular: string;
            primary: string;
            secondary: string;
            error: string;
            bg: {
                primary: string;
                secondary: string;
                footer: string;
            };
        };
        breakpoints: {
            xs: number;
            sm: number;
            md: number;
            lg: number;
        };
        breakepoint: (size: 'xs' | 'sm' | 'md' | 'lg') => (styles: TemplateStringsArray) => string;
    }
}
