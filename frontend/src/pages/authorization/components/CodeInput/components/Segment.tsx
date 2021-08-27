import React from 'react';
import styled from 'styled-components';

interface IProps {
    children: React.ReactNode;
    error?: boolean;
}

export const Segment: React.FC<IProps> = ({children, error}) => <Container error={error}>{children}</Container>;

const Container = styled.div<{error?: boolean}>`
    align-items: center;
    background-color: #ffffff;
    border: 1px solid ${(props) => (props.error ? '#E30B17' : '#C4C8DB')};
    border-radius: 4px;
    box-sizing: border-box;
    color: ${(props) => (props.error ? '#E30B17' : '#3A3463')};
    display: flex;
    font-size: 42px;
    font-weight: 500;
    height: 52px;
    justify-content: center;
    line-height: 28px;
    text-align: center;
    width: 42px;
`;
