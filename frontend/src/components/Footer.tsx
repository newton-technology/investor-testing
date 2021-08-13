import React from 'react';
import styled from 'styled-components';
import {Container} from './Container';

export const Footer: React.FC = () => {
    return (
        <FooterContainer>
            <Container>footer</Container>
        </FooterContainer>
    );
};

const FooterContainer = styled.div`
    margin-top: 100px;
    color: #fff;
    padding-top: 48px;
    padding-bottom: 48px;
    background-color: ${({theme}) => theme.palette.footerBg};
`;
