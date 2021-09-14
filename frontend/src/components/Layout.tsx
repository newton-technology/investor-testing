import React from 'react';
import styled from 'styled-components';

import {Container} from './Container';
import {Footer} from './Footer';
import {Navbar} from './Navbar';

export const Layout: React.FC = ({children}) => {
    return (
        <LayoutContainer>
            <MainContainer>
                <Navbar />
                <Container>{children}</Container>
            </MainContainer>
            <Footer />
        </LayoutContainer>
    );
};

const LayoutContainer = styled.div``;

const MainContainer = styled.div`
    min-height: 100vh;
    padding-bottom: 64px;

    ${({theme}) => theme.breakpoint('md')`
        padding-bottom: 100px;
    `}
`;
