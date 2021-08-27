import React from 'react';
import styled from 'styled-components';
import {Link} from 'react-router-dom';

import {Container} from './Container';
import {breakpoint} from '../theme/breakpont';

export const Footer: React.FC = () => {
    const phones = ['8 495 320-79-18', '8 495 320-79-18'];

    return (
        <FooterContainer>
            <Container>
                <FooterTop>
                    <PhonesTitle>Контакты</PhonesTitle>
                    <Phones>
                        {phones.map((phone: string, i: number) => {
                            return <Phone key={i}>{phone}</Phone>;
                        })}
                    </Phones>
                </FooterTop>
                <FooterMiddle>
                    Все права защищены © 1996 - 2020 АО ИК «ФОНДОВЫЙ КАПИТАЛ».
                    <br />
                    Лицензия на осуществление брокерской деятельности №045-10738-100000 от 13.11.2007. Выдана ФСФР
                    России. Без ограничения срока действия.
                    <br />
                    Лицензия на осуществление деятельности по управлению ценными бумагами № 045-13745-001000 от 21 марта
                    2013 г. Выдана ФСФР России. Без ограничения срока действия.
                    <br />
                    Лицензия на осуществление депозитарной деятельности № 045-13746-000100 от 21 марта 2013 года. Выдана
                    ФСФР России. Без ограничения срока действия.
                    <br />
                    Лицензия на осуществление дилерской деятельности № 045-10740-010000 от 13 ноября 2007 года. Выдана
                    ФСФР России. Без ограничения срока действия.
                </FooterMiddle>
                <FooterBottom>
                    <Link to='/'>Перейти на официальный сайт</Link>
                    <NewtonLink to='/'>Разработано Ньютон Технологии © 2021</NewtonLink>
                </FooterBottom>
            </Container>
        </FooterContainer>
    );
};

const FooterContainer = styled.div`
    margin-top: 64px;
    color: ${({theme}) => theme.palette.bg.secondary};
    padding-top: 38px;
    padding-bottom: 32px;
    background-color: ${({theme}) => theme.palette.bg.footer};

    ${breakpoint('md')`
        margin-top: 100px;
        padding-top: 48px;
        padding-bottom: 48px;
    `}
`;

const FooterTop = styled.div``;

const PhonesTitle = styled.div`
    font-size: 24px;
    margin-bottom: 20px;
    font-weight: 500;
`;

const Phones = styled.div`
    font-size: 32px;
    font-weight: 700;
    display: inline-grid;
    grid-gap: 20px;
    margin-bottom: 32px;

    ${breakpoint('md')`
        grid-gap: 32px;
        grid-template-columns: auto auto;
    `}
`;

const Phone = styled.div``;

const FooterMiddle = styled.div`
    font-size: 17px;
    padding: 32px 0;
    border-top: 1px solid #c4c8db;
    line-height: 1.7;
`;

const FooterBottom = styled.div`
    font-size: 20px;
    color: ${({theme}) => theme.palette.primary};

    ${breakpoint('md')`
        display: flex;
        justify-content: space-between;
    `}
`;

const NewtonLink = styled(Link)`
    margin-top: 60px;
    font-size: 16px;
    display: inline-block;

    ${breakpoint('md')`
        font-size: inherit;
        margin-top: 0;
    `}
`;
