import React from 'react';
import styled from 'styled-components';

import {Modal} from '../../../components/Modal';
import {Button} from '../../../components/Button';

interface IProps {
    isOpen: boolean;
    onClose?: () => void;
}

export const TestWarningModal: React.FC<IProps> = (props) => {
    const {isOpen, onClose} = props;

    return (
        <Modal isOpen={isOpen}>
            <Container>
                <Title>Завершить тест досрочно?</Title>
                <Subtitle>Ответы не будут сохранены</Subtitle>
                <ButtonsContainer>
                    <Button isPlain>Все равно выйти</Button>
                    <Button>Продолжить тест</Button>
                </ButtonsContainer>
            </Container>
        </Modal>
    );
};

const Container = styled.div`
    text-align: center;
    padding: 48px 56px 60px;
    background-color: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 10px;
`;

const Title = styled.div`
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 14px;
`;

const Subtitle = styled.div`
    font-size: 24px;
    margin-bottom: 54px;
`;

const ButtonsContainer = styled.div`
    display: grid;
    grid-gap: 20px;
    grid-template-columns: auto auto;
`;
