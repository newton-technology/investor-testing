import React, {ChangeEvent, SyntheticEvent, memo} from 'react';
import styled from 'styled-components';

import {Icon} from '../../../components/Icon';
interface IProps {
    value?: string;
    placeholder?: string;
    onChange?: (e: ChangeEvent<HTMLInputElement>) => void;
    onSubmit?: (e: SyntheticEvent, value?: string) => void;
}

const SearchInput: React.FC<IProps> = ({onChange, onSubmit, value, placeholder}) => {
    const onSubmitHandler = (event: SyntheticEvent) => {
        event.preventDefault();
        if (onSubmit) onSubmit(event, value);
    };

    return (
        <InputFormContainer onSubmit={onSubmitHandler}>
            {!value && <IconPlaceholder name='search' size={17} />}
            <Input onChange={onChange} value={value} placeholder={placeholder} />
            {value && (
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
    border-radius: 4px;
    font-size: 17px;
    line-height: 130%;
    padding: 14.28px 15px 14.28px 31px;
    width: 100%;

    &: focus-visible {
        border: 0;
        outline: 0;
    }

    &:placeholder {
        color: ${({theme}) => theme.palette.border.input};
    }
`;

const IconPlaceholder = styled(Icon)`
    left: 31px;
    margin-right: 16px;
    position: absolute;
    & path {
        fill: ${({theme}) => theme.palette.border.input};
    }
`;

const SubmitButton = styled.button`
    background: ${({theme}) => theme.palette.secondary};
    border-radius: 0px 4px 4px 0;
    line-height: 1;
    padding: 17px 20px 16.5px 19px;
`;

export default memo(SearchInput);
