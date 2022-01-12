import React from 'react';
import styled from 'styled-components';

import {ReactComponent as BackgroundImage} from '../../../assets/svg/moex-banner-background.svg';

interface IProps {
    className?: string;
}

const bannerLink = process.env.REACT_APP_MOEX_SCHOOL_BANNER_LINK;

const MoExBanner: React.FC<IProps> = (props) => (
    <Container href={bannerLink} target='_blank' className={props.className}>
        <PreTitle>Бесплатный курс от</PreTitle>
        <Title>Школы Московской Биржи</Title>
        <Description>
            Курс для начинающих инвесторов, разработанный экспертами Московской биржи, при поддержке Банка России.
        </Description>
        <BackgroundImage />
    </Container>
);

const Container = styled.a`
    position: relative;
    display: flex;
    flex-direction: column;
    background-color: ${({theme}) => theme.palette.bg.secondary};
    padding: 32px;
    border-radius: 10px;
    margin: 12px 0;

    svg#moex-background {
        position: absolute;
        top: 50%;
        right: 0;
        transform: translate(25px, -50%);
        width: 283px;
        height: 219px;

        .table {
            display: none;
        }
    }
`;

const PreTitle = styled.p`
    color: #8666e6;
    font-size: 18px;
    text-transform: uppercase;
`;

const Title = styled.p`
    color: #1e252e;
    font-size: 28px;
    font-weight: 700;
    line-height: 1.4;
    text-transform: uppercase;
    margin: 8px 0 27px;
    max-width: 293px;
`;

const Description = styled.p`
    font-size: 16px;
    line-height: 1.4;
    max-width: 319px;
`;

export default MoExBanner;
