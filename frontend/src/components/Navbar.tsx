import React from 'react';
import {Link, useHistory} from 'react-router-dom';
import styled from 'styled-components';

import {authService} from '../api/AuthService';
import {Container} from './Container';
import {Icon} from './Icon';

const logos = require.context('../assets/img', false, /logo\.(svg|png|jpe?g)$/);

export const Navbar: React.FC = () => {
    const history = useHistory();
    const module = logos.keys().map(logos)[0] as any;

    const logout = () => {
        authService.logout();
        history.push('/');
    };

    return (
        <NavContainer>
            <Container>
                <Nav>
                    <Logo to='/tests'>{module && <img src={module.default} />}</Logo>
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

const Logo = styled(Link)`
    margin-right: 40px;

    img {
        max-width: 100%;
        max-height: 50px;
    }
`;

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
