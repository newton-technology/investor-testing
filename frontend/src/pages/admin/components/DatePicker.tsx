import React, {ChangeEvent} from 'react';
import styled from 'styled-components';

import {Icon} from '../../../components/Icon';
import {todayDay} from '../../../utils/tableUtils';
import {TDate} from './hooks/useTableDates';

interface IProps {
    date: TDate;
    dateHandler: (event: ChangeEvent<HTMLInputElement>) => void;
    clear: () => void;
}

const DatePicker: React.FC<IProps> = ({date, dateHandler, clear}) => {
    return (
        <Container>
            <InputsContainer>
                <DateInputWrapper>
                    <StyledLabel>С</StyledLabel>
                    <DateInput value={date.dateStart} name='dateStart' max={todayDay} onChange={dateHandler} />
                </DateInputWrapper>
                <DateInputWrapper>
                    <StyledLabel>До</StyledLabel>
                    <DateInput value={date.dateEnd} name='dateEnd' max={todayDay} onChange={dateHandler} />
                </DateInputWrapper>
                {(date.dateEnd || date.dateStart) && (
                    <ClearButton onClick={clear}>
                        <CloseIcon size={20} />
                    </ClearButton>
                )}
            </InputsContainer>
        </Container>
    );
};

const Container = styled.div`
    width: 100%;
`;

const InputsContainer = styled.div`
    background: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 0 4px 4px 0;
    display: flex;
    padding: 10px 56px 10px 24px;
    position: relative;
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
    padding: 3px 8px 4px 8px;
    width: 100%;

    & span {
        padding: 0;
    }

    &::-webkit-clear-button {
        display: none !important;
    }
`;

const ClearButton = styled.div`
    align-items: center;
    background: ${({theme}) => theme.palette.secondary};
    border-radius: 0 4px 4px 0;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    height: 100%;
    justify-content: center;
    position: absolute;
    right: 0;
    top: 0;
    width: 52px;
`;

const CloseIcon = styled(Icon).attrs({name: 'close'})`
    & svg {
        fill: #fff;
        stroke: #fff;
    }
`;

export default DatePicker;
