import React from 'react';
import styled, {css} from 'styled-components';

import {Icon} from '../../../components/Icon';

export interface IAnswerControl {
    id: number;
    answer: string;
}

interface IProps {
    id: number;
    answer: string;
    questionId: number;
    isMultipleAnswers: boolean;
    getIsChecked: (questionId: number, answerId: number) => boolean;
    changeValue: (questionId: number, answerId: number, isMultipleAnswers: boolean) => void;
}

export const AnswerControl: React.FC<IProps> = (props) => {
    const {id, answer: text, questionId, isMultipleAnswers, getIsChecked, changeValue} = props;
    const isChecked = getIsChecked(questionId, id);

    const handleClick = () => {
        changeValue(questionId, id, isMultipleAnswers);
    };

    return (
        <Container onClick={handleClick}>
            {isMultipleAnswers ? (
                <CheckboxControl isChecked={isChecked}>
                    <Icon name='check' />
                </CheckboxControl>
            ) : (
                <RadioControl isChecked={isChecked} />
            )}
            <Text>{text}</Text>
        </Container>
    );
};

const Container = styled.div`
    display: flex;
    font-size: 17px;
    cursor: pointer;

    & + & {
        margin-top: 24px;
    }
`;

const controlCss = css`
    margin-right: 28px;
    margin-top: 3px;
    height: 24px;
    width: 24px;
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
`;

const CheckboxControl = styled.div<{isChecked: boolean}>`
    ${controlCss};
    border: 2px solid;
    border-color: ${({theme, isChecked}) => (isChecked ? theme.palette.primary : '#E3E6F4')};
    border-radius: 4px;
    color: ${({theme, isChecked}) => (isChecked ? theme.palette.primary : 'transparent')};
`;

const RadioControl = styled.div<{isChecked: boolean}>`
    ${controlCss};
    border: 1px solid;
    border-color: ${({theme, isChecked}) => (isChecked ? theme.palette.primary : theme.palette.regular)};
    position: relative;
    border-radius: 50%;

    &:before {
        display: ${({isChecked}) => (isChecked ? 'block' : 'none')};
        content: '';
        height: 14px;
        width: 14px;
        border-radius: 50%;
        background-color: ${({theme}) => theme.palette.primary};
    }
`;

const Text = styled.div``;
