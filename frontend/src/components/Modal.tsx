import React, {useLayoutEffect} from 'react';
import styled from 'styled-components';

import {Portal} from './Portal';

interface IProps {
    className?: string;
    isOpen: boolean;
    onClose?: () => void;
}

export const Modal: React.FC<IProps> = (props) => {
    const {className, isOpen, children, onClose} = props;

    useLayoutEffect(() => {
        if (isOpen) {
            document.documentElement.style.overflow = 'hidden';
        }
        return () => {
            document.documentElement.style.overflow = 'visible';
        };
    });

    const handleClose = () => {
        // onClose();
    };

    if (!isOpen) {
        return null;
    }

    return (
        <Portal>
            <ModalContainer>
                <ModalDialog>{children}</ModalDialog>
                <ModalOverlay onClick={handleClose} />
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
    overflow-y: auto;
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
