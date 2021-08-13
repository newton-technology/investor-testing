import React from 'react';
import styled from 'styled-components';
import {Navbar} from './Navbar';
import {Footer} from './Footer';
import {Container} from './Container';

export const Layout: React.FC = ({children}) => {
    return (
        <LayoutContainer>
            <Navbar />
            <Container>{children}</Container>
            <Footer />
        </LayoutContainer>
    );
};

const LayoutContainer = styled.div``;
