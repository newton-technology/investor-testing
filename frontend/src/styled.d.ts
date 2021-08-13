import 'styled-components';

declare module 'styled-components' {
    export interface DefaultTheme {
        palette: {
            regular: string;
            primary: string;
            secondary: string;
            mainBg: string;
            footerBg: string;
        };
    }
}
