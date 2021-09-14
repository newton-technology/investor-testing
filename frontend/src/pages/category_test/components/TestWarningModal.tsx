import {Location} from 'history';
import React, {useEffect, useState} from 'react';
import {Prompt, useHistory} from 'react-router-dom';
import styled from 'styled-components';

import {Button} from '../../../components/Button';
import {Modal} from '../../../components/Modal';

interface IProps {
    isBlocked: boolean;
}

export const TestWarningModal: React.FC<IProps> = ({isBlocked}) => {
    const [isModalOpen, setIsModalOpen] = useState<boolean>(false);

    const history = useHistory();
    const [lastLocation, setLastLocation] = useState<Location | undefined>();
    const [isNavigationConfirm, setIsNavigationConfirm] = useState(false);

    useEffect(() => {
        const unload = (e: BeforeUnloadEvent) => {
            if (isBlocked) {
                e.preventDefault();
                e.returnValue = '';
            }
        };
        window.addEventListener('beforeunload', unload);

        return () => {
            window.removeEventListener('beforeunload', unload);
        };
    }, [isBlocked]);

    useEffect(() => {
        if (isNavigationConfirm && lastLocation) {
            history.push(lastLocation.pathname);
        }
    }, [isNavigationConfirm, lastLocation, history]);

    const closeModal = () => {
        setIsModalOpen(false);
    };

    const handleBlockedRoute = (nextLocation: Location) => {
        if (!isNavigationConfirm && isBlocked) {
            setIsModalOpen(true);
            setLastLocation(nextLocation);
            return false;
        }

        return true;
    };

    const handleNavigationConfirm = () => {
        closeModal();
        setIsNavigationConfirm(true);
    };

    return (
        <>
            <Prompt when message={handleBlockedRoute} />
            <Modal isOpen={isModalOpen} onClose={closeModal}>
                <Container>
                    <Title>Завершить тест досрочно?</Title>
                    <Subtitle>Ответы не будут сохранены</Subtitle>
                    <ButtonsContainer>
                        <Button isPlain onClick={handleNavigationConfirm}>
                            Все равно выйти
                        </Button>
                        <Button onClick={closeModal}>Продолжить тест</Button>
                    </ButtonsContainer>
                </Container>
            </Modal>
        </>
    );
};

const Container = styled.div`
    text-align: center;
    padding: 24px;
    background-color: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 10px;

    ${({theme}) => theme.breakpoint('md')`
        padding: 48px 56px 60px;
    `}
`;

const Title = styled.div`
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 14px;

    ${({theme}) => theme.breakpoint('md')`
        font-size: 32px;
    `}
`;

const Subtitle = styled.div`
    margin-bottom: 32px;

    ${({theme}) => theme.breakpoint('md')`
        font-size: 24px;
        margin-bottom: 54px;
    `}
`;

const ButtonsContainer = styled.div`
    display: grid;
    grid-gap: 8px;

    ${({theme}) => theme.breakpoint('md')`
        grid-gap: 20px;
        grid-template-columns: auto auto;
    `}
`;
