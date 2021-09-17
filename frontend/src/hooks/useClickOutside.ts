import {useRef, useEffect, useCallback, MutableRefObject} from 'react';

export const useClickOutside = <T extends HTMLElement>(
    cb: () => void,
    trigger: boolean = false,
): MutableRefObject<T | null> => {
    const ref = useRef<T | null>(null);

    const clickOutside = useCallback(
        (e: MouseEvent): void => {
            if (trigger && ref.current && !ref.current.contains(e.target as Node)) {
                cb();
            }
        },
        [trigger, cb],
    );

    useEffect(() => {
        document.addEventListener('mousedown', clickOutside);
        return () => {
            document.removeEventListener('mousedown', clickOutside);
        };
    }, [trigger, clickOutside]);

    return ref;
};
