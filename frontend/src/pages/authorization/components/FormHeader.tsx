import React from 'react';
import styled from 'styled-components';

const logos = require.context('../../../assets/img', false, /logoOnLogin\.(svg|jpe?g|png)$/);

export const FormHeader: React.FC = () => {
    const module = logos.keys().map(logos)[0] as any;

    if (!module) {
        return null;
    }

    return (
        <Container>
            <img src={module.default} />
        </Container>
    );
};

const Container = styled.div`
    display: flex;
    justify-content: center;
    padding-top: 32px;

    img {
        max-height: 100px;
        max-width: 31%;
    }

    ${({theme}) => theme.breakpoint('md')`
        padding-bottom: 17px;
        
        img {
            max-width: 100%;
        }
    `}
`;
