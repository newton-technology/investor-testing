import dayjs from 'dayjs';
import React, {useEffect, useRef} from 'react';

interface ICountdown {
    countdown: number;
    formatedCountdown: string;
    restart: () => void;
}

const threeMinutes = 180000;

export const useCountdown = (timeoutMs: number = threeMinutes): ICountdown => {
    const timer = useRef<number>(0);
    const [countdown, setCountdown] = React.useState<number>(timeoutMs);

    const restart = () => {
        setCountdown(timeoutMs);
        timer.current = window.setInterval(() => setCountdown((prev) => prev - 1000), 1000);
    };

    useEffect(() => {
        timer.current = window.setInterval(() => setCountdown((prev) => prev - 1000), 1000);
        return () => clearInterval(timer.current);
    }, []);

    useEffect(() => {
        if (countdown <= 0) {
            clearInterval(timer.current);
        }
    }, [countdown]);

    return {countdown, formatedCountdown: dayjs(countdown).format('mm:ss'), restart};
};
