import React from 'react';
import styled from 'styled-components';

interface IProps {
    children: React.ReactNode;
    error?: boolean;
}

const Container = styled.div<{error?: boolean}>`
    align-items: center;
    background-color: #ffffff;
    border: 1px solid ${(props) => (props.error ? '#CD003E' : '#B8BDCE')};
    border-radius: 5px;
    box-sizing: border-box;
    color: ${(props) => (props.error ? '#CD003E' : '#222436')};
    display: flex;
    font-size: 24px;
    font-weight: 500;
    height: 56px;
    justify-content: center;
    line-height: 28px;
    text-align: center;
    width: 45px;
`;

export const Segment: React.FC<IProps> = ({children, error}) => <Container error={error}>{children}</Container>;
