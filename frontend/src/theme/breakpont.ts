interface IBreakpoints {
    sm: number;
    md: number;
    lg: number;
}

const breakpoints: IBreakpoints = {
    sm: 576,
    md: 768,
    lg: 1200,
};

export const breakpoint = (size: keyof IBreakpoints): any => {
    return (style: any) => `@media (min-width: ${breakpoints[size]}px) {${style}}`;
};
