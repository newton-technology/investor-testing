import {useEffect} from 'react';

export const useScrollToTop = (scrollDeps?: any[]) => {
    useEffect(() => {
        window.scrollTo(0, 0);
    }, scrollDeps || []);
};
