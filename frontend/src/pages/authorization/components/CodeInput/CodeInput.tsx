import React, {useEffect, useState} from 'react';
import styled from 'styled-components';

import {Segment} from './components/Segment';

interface IProps {
    length: number;
    error?: boolean;
    onComplete?: (value: string) => void;
    onChange: (value: string) => void;
    className?: string;
}

export const CodeInput = React.forwardRef<HTMLInputElement, IProps>(
    ({length, error, onComplete, onChange, className}, ref) => {
        const [value, setValue] = useState<string>('');
        const positions = new Array(length).fill(0);

        const changeHandle = (e: React.ChangeEvent<HTMLInputElement>) => {
            const newValue = (value + e.target.value).trim().slice(0, length);
            onChange(newValue);
            e.target.value = '';
            setValue(newValue);
        };

        const handleKeyUp = (e: React.KeyboardEvent) => {
            if (e.key === 'Backspace') {
                const newValue = value.slice(0, value.length - 1);
                setValue(newValue);
                onChange(newValue);
            }
        };

        const handlePaste = (e: React.ClipboardEvent<HTMLInputElement>) => {
            const newValue = e.clipboardData.getData('Text').trim().slice(0, length);
            setValue(newValue);
            onChange(newValue);
        };

        useEffect(() => {
            if (value.length === length && onComplete) {
                onComplete(value);
            }
        }, [value, length]);

        return (
            <Container className={className} htmlFor='code'>
                <Input
                    id='code'
                    ref={ref}
                    positionIndex={Math.min(length - 1, value.length)}
                    onChange={changeHandle}
                    onKeyUp={handleKeyUp}
                    onPaste={handlePaste}
                    error={error}
                    type='text'
                />
                <SegmentContainer>
                    {positions.map((_, index) => (
                        <Segment key={index} error={error}>
                            {value.slice(index, index + 1)}
                        </Segment>
                    ))}
                </SegmentContainer>
            </Container>
        );
    },
);

const Container = styled.label`
    align-items: center;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;

    div:not(:last-child) {
        margin-right: 10px;
    }
`;

const Input = styled.input<{positionIndex?: number; error?: boolean}>`
    background-color: transparent;
    border: none;
    box-sizing: border-box;
    font-size: 42px;
    height: 52px;
    left: ${({positionIndex = 1}) => (positionIndex + 1) * 42 + positionIndex * 8}px;
    outline: none;
    padding: 0 5px;
    position: absolute;
    top: 0;
    width: 42px;

    &:focus ~ div > div {
        ${(props) => !props.error && 'border-color: #0057B6;'}
    }
`;

const SegmentContainer = styled.div`
    display: flex;
    height: 52px;
`;
