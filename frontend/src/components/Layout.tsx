import React from 'react';
import styled from 'styled-components';

import {Container} from './Container';
import {Footer} from './Footer';
import {Navbar} from './Navbar';

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
