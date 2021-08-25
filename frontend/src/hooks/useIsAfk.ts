import {useEffect, useRef, useState} from 'react';

interface IAfk {
    isAfk: boolean;
}

const tenMinutes = 600000;

export const useIsAfk = (ms: number = tenMinutes): IAfk => {
    const timer = useRef<number>(0);
    const [isAfk, setIsAfk] = useState<boolean>(false);

    const resetTimer = () => {
        clearTimeout(timer.current);
        timer.current = window.setTimeout(() => setIsAfk(true), ms);
    };

    useEffect(() => {
        timer.current = window.setTimeout(() => setIsAfk(true), ms);

        return () => clearTimeout(timer.current);
    }, []);

    useEffect(() => {
        document.body.addEventListener('click', resetTimer);
        document.body.addEventListener('keydown', resetTimer);
        document.body.addEventListener('mouseover', resetTimer);
        return function cleanup() {
            window.removeEventListener('click', resetTimer);
            window.removeEventListener('keydown', resetTimer);
            window.removeEventListener('mouseover', resetTimer);
        };
    }, []);

    return {isAfk};
};
