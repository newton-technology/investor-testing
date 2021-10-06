import React, {useEffect} from 'react';
import styled from 'styled-components';

import {Segment} from './components/Segment';

interface IProps {
    value: string;
    setValue: React.Dispatch<React.SetStateAction<string>>;
    length: number;
    error?: boolean;
    onComplete?: (value: string) => void;
    onChange: (value: string) => void;
    className?: string;
}

export const CodeInput = React.forwardRef<HTMLInputElement, IProps>(
    ({value, setValue, length, error, onComplete, onChange, className}, ref) => {
        const positions = new Array(length).fill(0);

        const changeHandle = (e: React.ChangeEvent<HTMLInputElement>) => {
            e.preventDefault();
            setValue(e.target.value);
            onChange(e.target.value);
        };

        const handlePaste = (e: React.ClipboardEvent<HTMLInputElement>) => {
            e.preventDefault();
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
                <SegmentContainer>
                    <Input
                        id='code'
                        ref={ref}
                        value={value}
                        positionIndex={Math.min(length - 1, value.length)}
                        onChange={changeHandle}
                        onPaste={handlePaste}
                        error={error}
                        type='text'
                        maxLength={length}
                        inputMode='numeric'
                        autoComplete='false'
                    />
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
    margin: 0 auto;
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
    height: 32px;
    left: ${({positionIndex = 0}) => positionIndex * 52 + 21}px;
    max-width: 0.5px;
    outline: none;
    padding: 0;
    position: absolute;
    -webkit-text-fill-color: transparent;
    top: 10px;
    width: 0.5px;

    &::selection {
        background: black;
    }

    &:focus ~ div > div {
        ${(props) => !props.error && 'border-color: #0057B6;'}
    }
`;

const SegmentContainer = styled.div`
    display: flex;
    height: 52px;
`;
