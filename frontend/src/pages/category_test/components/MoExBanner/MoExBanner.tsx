import React from 'react';
import styled from 'styled-components';

import {ReactComponent as BannerLogo} from './moex-school-logo.svg';
import {ReactComponent as BackgroundImage} from './bg-image.svg';

const MoExBanner: React.FC = (props) => (
    <Container>
        <Background>
            <BackgroundImage />
        </Background>
        <BannerLogo />
        <Title>&laquo;Путь инвестора&raquo;</Title>
        <Subtitle>Бесплатный курс от школы Московской Биржи</Subtitle>
    </Container>
);

const Container = styled.div`
    position: relative;
    display: flex;
    flex-direction: column;
    background-color: ${({theme}) => theme.palette.bg.secondary};
    padding: 24px;
    margin: 41px 0;
    border-radius: 10px;
    text-align: center;

    ${({theme}) => theme.breakpoint('md')`
         text-align: left;
         padding: 24px 32px;
    `}
`;

const Title = styled.p`
    color: ${({theme}) => theme.palette.featured};
    font-size: 28px;
    font-weight: 700;
    line-height: 1.4;
    text-transform: uppercase;
    margin: 24px 0 12px;

    ${({theme}) => theme.breakpoint('md')`
         font-size: 24px;
         margin: 0;
    `}
`;

const Subtitle = styled.p`
    font-size: 18px;
    font-weight: 400;
    text-transform: uppercase;
    max-width: 270px;
    margin: 0 auto 213px;

    ${({theme}) => theme.breakpoint('md')`
         font-size: 14px;
         max-width: none;
         margin: 0;
    `}
`;

const Background = styled.div`
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;

    svg {
        position: absolute;
        bottom: 0;
        right: 0;
        transform: translate(84px, 42px);
        width: 338px;
        height: 263px;

        [data-hidden-mb] {
            display: none;
        }

        ${({theme}) => theme.breakpoint('md')`
            bottom: 3px;
            right: 2px;
            transform: none;
            width: 170px;
            height: 132px;
            
            [data-hidden-mb] {
                display: inherit;
            }
        `}
    }
`;

export default MoExBanner;
