import React from 'react';
import styled from 'styled-components';

import {ReactComponent as Logo} from '../../../assets/svg/logo.svg';

export const FormHeader: React.FC = () => {
    return (
        <Container>
            <Logo />
        </Container>
    );
};
const Container = styled.div`
    padding-bottom: 17px;
    padding-top: 32px;
`;
