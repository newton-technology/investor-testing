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
                darkBlue: string;
            };
            border: {
                input: string;
                authorization: string;
                primary: string;
                secondary: string;
                footer: string;
            };
        };
        breakpoints: {
            sm: number;
            md: number;
            lg: number;
        };
        breakpoint: (size: 'sm' | 'md' | 'lg') => (styles: TemplateStringsArray) => string;
    }
}
