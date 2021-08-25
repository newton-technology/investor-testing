import React, {ChangeEvent, SyntheticEvent, memo} from 'react';
import styled from 'styled-components';

import {Icon} from '../../../components/Icon';
import {useToggle} from '../../../hooks/useToggle';

interface IProps {
    value?: string;
    placeholder?: string;
    onChange?: (e: ChangeEvent<HTMLInputElement>) => void;
    onSubmit?: (e: SyntheticEvent, value?: string) => void;
}

const SearchInput: React.FC<IProps> = ({onChange, onSubmit, value, placeholder}) => {
    const {state, setActive, setDisabled} = useToggle();

    const onSubmitHandler = (event: SyntheticEvent) => {
        event.preventDefault();
        if (onSubmit) onSubmit(event, value);
    };

    return (
        <InputFormContainer onSubmit={onSubmitHandler}>
            <Input onChange={onChange} value={value} onFocus={setActive} onBlur={setDisabled} />
            {((!state && placeholder) || !value) && (
                <Placeholder>
                    <IconPlaceholder name='search' size={17} />
                    <span>{placeholder}</span>
                </Placeholder>
            )}
            {state && value && (
                <SubmitButton type='submit'>
                    <Icon name='search' size={17} />
                </SubmitButton>
            )}
        </InputFormContainer>
    );
};

const InputFormContainer = styled.form`
    align-items: center;
    background: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 4px;
    display: flex;
    max-height: 52px;
    position: relative;
    width: 100%;
`;

const Input = styled.input`
    border: 0;
    font-size: 17px;
    line-height: 130%;
    padding: 14.28px 31px;
    width: 100%;

    &: focus-visible {
        border: 0;
        outline: 0;
    }
`;

const Placeholder = styled.div`
    align-items: center;
    color: ${({theme}) => theme.palette.border.input};
    display: flex;
    left: 31px;
    line-height: 22px;
    position: absolute;
`;

const IconPlaceholder = styled(Icon)`
    margin-right: 16px;
    & path {
        fill: ${({theme}) => theme.palette.border.input};
    }
`;

const SubmitButton = styled.button`
    background: ${({theme}) => theme.palette.bg.darkBlue};
    line-height: 1;
    padding: 17px 20px 16.5px 19px;
`;

export default memo(SearchInput);
