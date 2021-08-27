import React from 'react';
import {Link, useHistory} from 'react-router-dom';
import styled from 'styled-components';

import {authService} from '../api/AuthService';
import {Container} from './Container';
import {Icon} from './Icon';

export const Navbar: React.FC = () => {
    const history = useHistory();

    const logout = () => {
        authService.logout();
        history.push('/');
    };

    return (
        <NavContainer>
            <Container>
                <Nav>
                    <Logo to='/tests'>Главная</Logo>
                    <LogoutButton onClick={logout}>
                        <IconContainer name='arrow_right' />
                        Выйти
                    </LogoutButton>
                </Nav>
            </Container>
        </NavContainer>
    );
};

const NavContainer = styled.div`
    box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 40px;
    padding-bottom: 17px;
    padding-top: 17px;
`;

const Nav = styled.div`
    display: flex;
    justify-content: space-between;
`;

const Logo = styled(Link)``;

const LogoutButton = styled.div`
    align-items: center;
    color: ${({theme}) => theme.palette.secondary};
    cursor: pointer;
    display: flex;
    font-size: 17px;
`;

const IconContainer = styled(Icon)`
    margin-right: 8px;
`;
