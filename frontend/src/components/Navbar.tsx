import React from 'react';
import styled from 'styled-components';
import {Link} from 'react-router-dom';

import {Container} from './Container';
import {Icon} from './Icon';
import logo from '../logo.png';
import {breakpoint} from '../theme/breakpont';

export const Navbar: React.FC = () => {
    return (
        <NavContainer>
            <Container>
                <Nav>
                    <Logo to='/tests'>
                        <img src={logo} />
                    </Logo>
                    <LogoutButton>
                        <IconContainer name='arrow_right' />
                        Выйти
                    </LogoutButton>
                </Nav>
            </Container>
        </NavContainer>
    );
};

const NavContainer = styled.div`
    padding-top: 17px;
    padding-bottom: 17px;
    box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 40px;
`;

const Nav = styled.div`
    display: flex;
    justify-content: space-between;
`;

const Logo = styled(Link)`
    margin-right: 40px;

    img {
        max-width: 100%;
    }
`;

const LogoutButton = styled.div`
    font-size: 17px;
    color: ${({theme}) => theme.palette.secondary};
    display: flex;
    align-items: center;
    cursor: pointer;
`;

const IconContainer = styled(Icon)`
    margin-right: 8px;
`;
