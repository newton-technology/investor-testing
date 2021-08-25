import React, {useState, useRef, useCallback, useEffect, SyntheticEvent, memo} from 'react';
import styled from 'styled-components';

import {Icon} from '../../../components/Icon';
import {useToggle} from '../../../hooks/useToggle';

type Option = {
    title: string;
    value: string | null;
};

interface IProps {
    options: Option[];
    value?: string | null;
    onChange?: (e: SyntheticEvent, data: Option) => void;
}

const Select: React.FC<IProps> = ({options, onChange, value}) => {
    const {state: isOpen, toggle, setDisabled} = useToggle(false);
    const [selected, setSelected] = useState<Option>(options.find((option) => option.value === value) || options[0]);
    const selectRef = useRef<HTMLDivElement | null>(null);

    const clickOutside = useCallback(
        (e: MouseEvent): void => {
            if (isOpen && selectRef.current && !selectRef.current.contains(e.target as Node)) {
                setDisabled();
            }
        },
        [isOpen, setDisabled],
    );

    const selectOption = useCallback(
        (option: Option) => (e: SyntheticEvent) => {
            setSelected(option);
            if (onChange) onChange(e, option);
        },
        [onChange],
    );

    useEffect(() => {
        document.addEventListener('mousedown', clickOutside);
        return () => {
            document.removeEventListener('mousedown', clickOutside);
        };
    }, [isOpen, clickOutside]);

    return (
        <Container ref={selectRef} $isOpen={isOpen} onClick={toggle}>
            <SelectLabel $isOpen={isOpen}>{selected.title}</SelectLabel>
            <StyledIcon name='chevron_right' size={24} $isOpen={isOpen} />
            {isOpen && (
                <OptionsContainer>
                    {options
                        .filter((option) => option.value !== selected.value)
                        .map((option) => (
                            <div key={option.value} onClick={selectOption(option)}>
                                {option.title}
                            </div>
                        ))}
                </OptionsContainer>
            )}
        </Container>
    );
};

const Container = styled.div<{$isOpen: boolean}>`
    align-items: center;
    background: ${({theme, $isOpen}) => ($isOpen ? theme.palette.bg.darkBlue : theme.palette.bg.secondary)};
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    padding: 14px 24px;
    position: relative;
    width: 100%;
`;

const SelectLabel = styled.label<{$isOpen: boolean}>`
    color: ${({theme, $isOpen}) => ($isOpen ? theme.palette.bg.secondary : theme.palette.bg.darkBlue)};
    font-size: 17px;
    line-height: 22px;
    user-select: none;
`;

const OptionsContainer = styled.div`
    background: ${({theme}) => theme.palette.bg.darkBlue};
    border-radius: 0px 0px 4px 4px;
    bottom: -36px;
    color: ${({theme}) => theme.palette.bg.secondary};
    font-size: 17px;
    left: 0;
    line-height: 22px;
    padding: 2px 24px 14px 24px;
    position: absolute;
    width: 100%;
`;

const StyledIcon = styled(Icon)<{$isOpen: boolean}>`
    margin-left: auto;
    transform: ${({$isOpen}) => ($isOpen ? 'rotate(90deg)' : 'rotate(0deg)')};

    & path {
        ${({$isOpen, theme}) => ($isOpen ? `fill: ${theme.palette.bg.secondary}` : '')};
    }
`;

// const;

export default memo(Select);
