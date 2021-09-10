import React from 'react';
import styled from 'styled-components';

const DatePicker: React.FC = () => {
    return (
        <Container>
            <InputsContainer>
                <DateInputWrapper>
                    <StyledLabel>С</StyledLabel>
                    <DateInput />
                </DateInputWrapper>
                <DateInputWrapper>
                    <StyledLabel>До</StyledLabel>
                    <DateInput />
                </DateInputWrapper>
            </InputsContainer>
        </Container>
    );
};

const Container = styled.div`
    width: 100%;
`;

const InputsContainer = styled.div`
    background: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 0px 4px 4px 0px;
    display: flex;
    padding: 10px 24px;
`;

const DateInputWrapper = styled.div`
    align-items: center;
    display: flex;
`;

const StyledLabel = styled.label`
    margin-right: 8px;
`;

const DateInput = styled.input.attrs({type: 'date', required: true})`
    border: 1px solid ${({theme}) => theme.palette.border.input};
    border-radius: 4px;
    font-family: 'IBM Plex Sans', sans-serif;
    font-size: 17px;
    font-weight: 22px;
    margin-right: 24px;
    max-width: 142px;
    padding: 3px 8px 4px 8px;
    width: 100%;

    & span {
        padding: 0;
    }

    &::-webkit-clear-button {
        display: none !important;
    }
`;

export default DatePicker;
