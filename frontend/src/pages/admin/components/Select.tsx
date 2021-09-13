import isEqual from 'lodash.isequal';
import React, {useState, useCallback, SyntheticEvent, memo} from 'react';
import styled from 'styled-components';

import {Status} from '../../../api/ManagmentApi';
import {Icon} from '../../../components/Icon';
import {useClickOutside} from '../../../hooks/useClickOutside';
import {useToggle} from '../../../hooks/useToggle';
import {Option} from '../AllTestsPage';

interface IProps {
    options: Option[];
    value?: Status[];
    onChange?: (e: SyntheticEvent, data: Option) => void;
}

const Select: React.FC<IProps> = ({options, onChange, value}) => {
    const {state: isOpen, toggle, setDisabled} = useToggle(false);
    const [selected, setSelected] = useState<Option>(options.find((option) => option.value === value) || options[0]);
    const selectRef = useClickOutside<HTMLDivElement>(setDisabled, isOpen);

    const selectOption = useCallback(
        (option: Option) => (e: SyntheticEvent) => {
            setSelected(option);
            if (onChange) onChange(e, option);
        },
        [onChange],
    );

    return (
        <Container ref={selectRef} $isOpen={isOpen} onClick={toggle}>
            <SelectLabel $isOpen={isOpen}>{selected.title}</SelectLabel>
            <StyledIcon name='chevron_right' size={24} $isOpen={isOpen} />
            {isOpen && (
                <OptionsContainer>
                    {options
                        .filter((option) => !isEqual(option.value, value))
                        .map((option) => (
                            <StyledOption key={option.value.toString()} onClick={selectOption(option)}>
                                {option.title}
                            </StyledOption>
                        ))}
                </OptionsContainer>
            )}
        </Container>
    );
};

const Container = styled.div<{$isOpen: boolean}>`
    align-items: center;
    background: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    padding: 14px 24px;
    position: relative;
    width: 100%;
`;

const SelectLabel = styled.label<{$isOpen: boolean}>`
    color: ${({theme}) => theme.palette.regular};
    cursor: pointer;
    font-size: 17px;
    line-height: 22px;
    user-select: none;
`;

const OptionsContainer = styled.div`
    background: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 0px 0px 4px 4px;
    top: 52px;
    color: ${({theme}) => theme.palette.regular};
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
        // ${({$isOpen, theme}) => ($isOpen ? `fill: ${theme.palette.bg.secondary}` : '')};
    }
`;

const StyledOption = styled.div`
    margin-bottom: 14px;
`;

export default memo(Select);
