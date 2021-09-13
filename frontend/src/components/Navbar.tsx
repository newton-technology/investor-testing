import React from 'react';
import {Link, useHistory, useLocation} from 'react-router-dom';
import styled from 'styled-components';

import {authService} from '../api/AuthService';
import {Container} from './Container';
import {Icon} from './Icon';
interface IProps {
    isAdmin?: boolean;
}
const logos = require.context('../assets/img', false, /logo\.(svg|png|jpe?g)$/);

const pages = [
    {title: 'Первая страница', link: '/1'},
    {title: 'Вторая страница', link: '/2'},
    {title: 'Третья страница', link: '/3'},
];

export const Navbar: React.FC<IProps> = ({isAdmin}) => {
    const history = useHistory();
    const {pathname} = useLocation();
    const module = logos.keys().map(logos)[0] as any;
    const logoUrl = isAdmin ? '/admin/tests' : '/tests';

    const logout = () => {
        authService.logout();
        history.push('/');
    };

    return (
        <NavContainer>
            <Container>
                <Nav>
                    <Logo to={logoUrl}>{module && <img src={module.default} />}</Logo>
                    {isAdmin && (
                        <NavLinks>
                            {pages.map((page) => (
                                <StyledNavLink key={page.link} to={page.link} $isActive={pathname === page.link}>
                                    {page.title}
                                </StyledNavLink>
                            ))}
                        </NavLinks>
                    )}
                    {isAdmin && (
                        <DownloadButton>
                            <IconContainer name='download' />
                            Скачать файл
                        </DownloadButton>
                    )}
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

const DownloadButton = styled(LogoutButton)`
    margin-right: 32px;
`;

const IconContainer = styled(Icon)`
    margin-right: 8px;
`;

const NavLinks = styled.div`
    align-items: center;
    display: flex;
    margin: 0 auto;
`;

const StyledNavLink = styled(Link)<{$isActive: boolean}>`
    color: ${({$isActive, theme}) => ($isActive ? theme.palette.primary : theme.palette.regular)};
    font-size: 17px;
    font-weight: 500;
    line-height: 22px;
    margin: 0 12px;

    &:hover {
        color: ${({theme}) => theme.palette.primary};
    }
`;
