import 'styled-components';

declare module 'styled-components' {
    export interface DefaultTheme {
        palette: {
            regular: string;
            primary: string;
            secondary: string;
            bg: {
                primary: string;
                secondary: string;
                footer: string;
                darkBlue: string;
            };
            border: {
                input: string;
            };
        };
    }
}
