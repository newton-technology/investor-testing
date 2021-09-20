import {useState} from 'react';

interface IResult {
    state: boolean;
    toggle: () => void;
    setActive: () => void;
    setDisabled: () => void;
}

export const useToggle = (initialValue: boolean = false): IResult => {
    const [state, setState] = useState<boolean>(initialValue);

    const toggle = () => {
        setState((prevValue) => !prevValue);
    };

    const setActive = () => {
        setState(true);
    };

    const setDisabled = () => {
        setState(false);
    };

    return {state, toggle, setActive, setDisabled};
};
