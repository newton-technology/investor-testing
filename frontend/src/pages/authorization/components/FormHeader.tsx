import React from 'react';
import styled from 'styled-components';

import {ReactComponent as Logo} from '../../../assets/svg/logo.svg';

export const FormHeader: React.FC = () => {
    return (
        <Wrapper>
            <Logo />
        </Wrapper>
    );
};
const Wrapper = styled.div`
    padding-bottom: 17px;
    padding-top: 32px;
`;
