import React, {useLayoutEffect} from 'react';
import styled from 'styled-components';

import {Portal} from './Portal';

interface IProps {
    className?: string;
    isOpen: boolean;
    onClose: () => void;
}

export const Modal: React.FC<IProps> = (props) => {
    const {isOpen, children, onClose} = props;

    useLayoutEffect(() => {
        if (isOpen) {
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflowY = 'scroll';
        }
        return () => {
            document.documentElement.style.overflow = 'visible';
            document.body.style.overflowY = 'auto';
        };
    });

    if (!isOpen) {
        return null;
    }

    return (
        <Portal>
            <ModalContainer>
                <ModalDialog>{children}</ModalDialog>
                <ModalOverlay onClick={onClose} />
            </ModalContainer>
        </Portal>
    );
};

const ModalContainer = styled.div`
    position: fixed;
    display: flex;
    justify-content: center;
    padding: 20px;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    z-index: 1000;
    overflow-x: hidden;
    overflow-y: hidden;
`;

const ModalDialog = styled.div`
    z-index: 2;
    position: relative;
    top: 10%;
`;

const ModalOverlay = styled.div`
    background-color: ${({theme}) => theme.palette.bg.footer};
    opacity: 0.4;
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
`;
