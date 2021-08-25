import {useState, useCallback} from 'react';

interface IResult {
    state: boolean;
    toggle: () => void;
    setActive: () => void;
    setDisabled: () => void;
}

export const useToggle = (initialValue: boolean = false): IResult => {
    const [state, setState] = useState<boolean>(initialValue);

    const toggle = useCallback(() => {
        setState((prevValue) => !prevValue);
    }, []);

    const setActive = useCallback(() => {
        setState(true);
    }, []);

    const setDisabled = useCallback(() => {
        setState(false);
    }, []);

    return {state, toggle, setActive, setDisabled};
};
