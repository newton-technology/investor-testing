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
                    {site && <BrokerSiteLink href={site}>Перейти на официальный сайт</BrokerSiteLink>}
                    <NewtonSiteLink href='https://nwtn.io/'>Разработано Ньютон Технологии © 2021</NewtonSiteLink>
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

    ${({theme}) => theme.breakpoint('md')`
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

    ${({theme}) => theme.breakpoint('md')`
        grid-gap: 32px;
        grid-template-columns: auto auto;
    `}
`;

const Phone = styled.a``;

const FooterMiddle = styled.div`
    font-size: 17px;
    padding-bottom: 32px;
    line-height: 1.7;

    &:not(:first-child) {
        border-top: 1px solid #c4c8db;
        padding-top: 32px;
    }

    p + p {
        margin-top: 16px;

        ${({theme}) => theme.breakpoint('md')`
            margin-top: 0px;
        `}
    }
`;

const FooterBottom = styled.div`
    font-size: 20px;
    color: ${({theme}) => theme.palette.primary};
    display: flex;
    flex-direction: column;

    ${({theme}) => theme.breakpoint('md')`
        flex-direction: row;
        justify-content: space-between;
    `}

    a:hover {
        color: ${({theme}) => theme.palette.secondary};
        transition: color 0.2s ease-in-out;
    }
`;

const BrokerSiteLink = styled.a`
    margin-bottom: 60px;

    ${({theme}) => theme.breakpoint('md')`
        margin-bottom: 0;
    `}
`;

const NewtonSiteLink = styled.a`
    font-size: 16px;

    ${({theme}) => theme.breakpoint('md')`
        font-size: inherit;
        margin-top: 0;
    `}
`;
