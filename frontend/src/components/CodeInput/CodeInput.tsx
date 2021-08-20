import React, {useEffect, useState} from 'react';
import styled from 'styled-components';

import {Segment} from './components/Segment';

interface IProps {
    length: number;
    errorMessage?: string;
    onComplete?: (value: string) => void;
    onChange?: (value: string) => void;
    className?: string;
}

export const CodeInput = React.forwardRef<HTMLInputElement, IProps>(
    ({length, errorMessage, onComplete, onChange, className}, ref) => {
        const [value, setValue] = useState('');
        const positions = new Array(length).fill(0);

        const changeHandle = (e: React.ChangeEvent<HTMLInputElement>) => {
            const newValue = (value + e.target.value).trim().slice(0, length);
            if (onChange) {
                onChange(newValue);
            }
            e.target.value = '';
            setValue(newValue);
        };
        const handleKeyUp = (e: React.KeyboardEvent) => {
            if (e.key === 'Backspace') {
                setValue(value.slice(0, value.length - 1));
            }
        };
        const onPaste = (e: React.ClipboardEvent<HTMLInputElement>) => {
            const newValue = e.clipboardData.getData('Text').trim().slice(0, length);
            setValue(newValue);
        };

        useEffect(() => {
            if (value.length === length && onComplete) {
                onComplete(value);
            }
        }, [value, length, onComplete]);

        return (
            <Container className={className}>
                <Input
                    ref={ref}
                    positionIndex={Math.min(length - 1, value.length)}
                    isLast={value.length === length}
                    onChange={changeHandle}
                    onKeyUp={handleKeyUp}
                    onPaste={onPaste}
                    error={!!errorMessage}
                    type='text'
                />
                <SegmentContainer>
                    {positions.map((_, index) => (
                        <Segment key={index} error={!!errorMessage}>
                            {value.slice(index, index + 1)}
                        </Segment>
                    ))}
                </SegmentContainer>
                {!!errorMessage && <ErrorMessage>{errorMessage}</ErrorMessage>}
            </Container>
        );
    },
);

const Container = styled.div`
    align-items: center;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;

    div:not(:last-child) {
        margin-right: 10px;
    }
`;

const Input = styled.input<{positionIndex?: number; isLast?: boolean; error?: boolean}>`
    background-color: transparent;
    border: none;
    box-sizing: border-box;
    font-size: 32px;
    height: 56px;
    left: ${({positionIndex}) => (positionIndex || 1) * 55}px;
    outline: none;
    padding: 0 5px;
    position: absolute;
    text-align: ${({isLast}) => (isLast ? 'right' : 'left')};
    top: 0;
    width: 45px;

    &:focus ~ div > div {
        ${(props) => !props.error && 'border-color: #0057B6;'}
    }
`;

const SegmentContainer = styled.div`
    display: flex;
    height: 56px;
`;

const ErrorMessage = styled.span`
    color: #cd003e;
    display: block;
    font-size: 14px;
    line-height: 140%;
    padding-bottom: 10px;
`;
