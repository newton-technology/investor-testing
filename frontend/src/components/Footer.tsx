import React from 'react';
import styled from 'styled-components';

import customize from '../customize.json';
import {Container} from './Container';

const {phones, site, license} = customize.content || {};

export const Footer: React.FC = () => {
    return (
        <FooterContainer>
            <Container>
                {phones && Array.isArray(phones) && (
                    <FooterTop>
                        <PhonesTitle>Контакты</PhonesTitle>
                        <Phones>
                            {phones.map((phone: string, i: number) => {
                                return (
                                    <Phone key={i} href={`tel:${phone}`}>
                                        {phone}
                                    </Phone>
                                );
                            })}
                        </Phones>
                    </FooterTop>
                )}
                {license && (
                    <FooterMiddle>
                        <div dangerouslySetInnerHTML={{__html: license}} />
                    </FooterMiddle>
                )}
                <FooterBottom>
                    {site && <a href={site}>Перейти на официальный сайт</a>}
                    <a href='https://nwtn.io/'>Разработано Ньютон Технологии © 2021</a>
                </FooterBottom>
            </Container>
        </FooterContainer>
    );
};

const FooterContainer = styled.div`
    color: ${({theme}) => theme.palette.bg.secondary};
    padding: 32px 0;
    background-color: ${({theme}) => theme.palette.bg.footer};

    ${({theme}) => theme.breakpoint('md')`
        padding: 48px 0;
    `}
`;

const FooterTop = styled.div``;

const PhonesTitle = styled.div`
    font-size: 17px;
    margin-bottom: 16px;
    font-weight: 700;

    ${({theme}) => theme.breakpoint('md')`
        font-size: 24px;
        font-weight: 500;
        margin-bottom: 20px;
    `}
`;

const Phones = styled.div`
    display: inline-grid;
    font-size: 16px;
    grid-template-columns: auto auto;
    grid-gap: 54px;
    margin-bottom: 24px;

    ${({theme}) => theme.breakpoint('md')`
        margin-bottom: 32px;
        font-size: 32px;
        font-weight: 700;
        grid-gap: 32px;
    `}
`;

const Phone = styled.a``;

const FooterMiddle = styled.div`
    font-size: 14px;
    padding-bottom: 24px;
    line-height: 1.7;

    ${({theme}) => theme.breakpoint('md')`
        font-size: 17px;
        padding-bottom: 32px;
    `}

    &:not(:first-child) {
        border-top: 1px solid #c4c8db;
        padding-top: 24px;

        ${({theme}) => theme.breakpoint('md')`
            padding-bottom: 32px;
        `}
    }

    p + p {
        margin-top: 16px;

        ${({theme}) => theme.breakpoint('md')`
            margin-top: 0px;
        `}
    }
`;

const FooterBottom = styled.div`
    font-size: 16px;
    color: ${({theme}) => theme.palette.primary};
    display: flex;
    flex-direction: column;

    ${({theme}) => theme.breakpoint('md')`
        font-size: 20px;
        flex-direction: row;
        justify-content: space-between;
    `}

    a:hover {
        color: ${({theme}) => theme.palette.secondary};
        transition: color 0.2s ease-in-out;
    }

    a + a {
        margin-top: 16px;

        ${({theme}) => theme.breakpoint('md')`
            margin-top: 0;
        `}
    }
`;
