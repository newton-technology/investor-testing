import React from 'react';
import styled from 'styled-components';

import {ReactComponent as BannerLogo} from '../../../assets/svg/moex-school-logo.svg';
import {ReactComponent as BackgroundImage} from '../../../assets/svg/moex-banner-background.svg';

interface IProps {
    className?: string;
}

const bannerLink = process.env.REACT_APP_MOEX_SCHOOL_BANNER_LINK;

const MoExBanner: React.FC<IProps> = (props) => (
    <Container href={bannerLink} target='_blank' className={props.className}>
        <BackgroundImage />
        <BannerLogo />
        <Title>&laquo;Путь инвестора&raquo;</Title>
        <Subtitle>Бесплатный курс от школы Московской Биржи</Subtitle>
    </Container>
);

const Container = styled.a`
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    background-color: ${({theme}) => theme.palette.bg.secondary};
    padding: 24px 24px 213px;
    margin: 41px 0;
    border-radius: 10px;
    text-align: center;

    ${({theme}) => theme.breakpoint('md')`
         text-align: left;
         padding: 24px 32px;
    `}

    svg#moex-background {
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
    margin: 0 auto;

    ${({theme}) => theme.breakpoint('md')`
         font-size: 14px;
         max-width: none;
         margin: 0;
    `}
`;

export default MoExBanner;
