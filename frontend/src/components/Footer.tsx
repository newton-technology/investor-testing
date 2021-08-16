import React from 'react';
import styled from 'styled-components';
import {Link} from 'react-router-dom';

import {Container} from './Container';

export const Footer: React.FC = () => {
    const phones = ['8 495 320-79-18', '8 495 320-79-18'];

    return (
        <FooterContainer>
            <Container>
                <FooterTop>
                    <Title>Контакты</Title>
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
                    <Link to='/'>Разработано Ньютон Технологии © 2021</Link>
                </FooterBottom>
            </Container>
        </FooterContainer>
    );
};

const FooterContainer = styled.div`
    margin-top: 100px;
    color: ${({theme}) => theme.palette.bg.secondary};
    padding-top: 48px;
    padding-bottom: 48px;
    background-color: ${({theme}) => theme.palette.bg.footer};
`;

const FooterTop = styled.div``;

const Title = styled.div`
    font-size: 24px;
    margin-bottom: 24px;
`;

const Phones = styled.div`
    display: flex;
    font-size: 32px;
    font-weight: 600;
`;

const Phone = styled.div`
    & + & {
        margin-left: 32px;
    }
`;

const FooterMiddle = styled.div`
    font-size: 17px;
    padding: 32px 0;
    margin-top: 48px;
    border-top: 1px solid #c4c8db;
    line-height: 1.7;
`;

const FooterBottom = styled.div`
    display: flex;
    justify-content: space-between;
    font-size: 20px;
    color: ${({theme}) => theme.palette.primary};
`;
