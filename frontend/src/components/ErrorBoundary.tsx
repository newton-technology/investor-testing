import React from 'react';
import styled from 'styled-components';

import {ServerErrorMessage} from './ServerErrorMessage';
import {Container} from './Container';
import {Button} from './Button';

interface IProps {
    children: React.ReactNode;
}

interface IState {
    isError: boolean;
}

export class ErrorBoundary extends React.Component<IProps, IState> {
    static getDerivedStateFromError() {
        return {isError: true};
    }

    state: IState = {isError: false};

    render() {
        if (this.state.isError) {
            return (
                <ErrorMessageContainer>
                    <ServerErrorMessage
                        title='Ошибка приложения'
                        subtitle='В приложении произошла непредвиденная ошибка'>
                        <SupportLink href='mailto:investor_testing@nwtn.io'>Написать в поддержку</SupportLink>
                    </ServerErrorMessage>
                </ErrorMessageContainer>
            );
        }
        return this.props.children;
    }
}

const ErrorMessageContainer = styled(Container)`
    margin-top: 40px;
`;

const SupportLink = styled.a`
    color: ${({theme}) => theme.palette.primary};

    &:hover {
        text-decoration: underline;
    }
`;
