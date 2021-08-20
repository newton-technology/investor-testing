import React from 'react';
import styled from 'styled-components';

import {Button} from '../../../components/Button';
import {Icon} from '../../../components/Icon';
import {ReactComponent as BgImage} from './result_bg.svg';

interface IProps {
    isSuccess: boolean;
}

const title = {
    success: 'Тест пройден успешно',
    fail: 'Тест не пройден',
};

const subtitle = {
    success: 'Уведомление об оценке результатов\nскоро придет на вашу почту',
    fail: 'Наберите максимальный балл для\nпрохождения теста',
};

export const TestResult: React.FC<IProps> = (props) => {
    const {isSuccess} = props;

    return (
        <Container>
            <Content>
                <IconContainer name='planet' size={142} isSuccess={isSuccess} />
                <Title isSuccess={isSuccess}>{isSuccess ? title.success : title.fail}</Title>
                <Subtitle>{isSuccess ? subtitle.success : subtitle.fail}</Subtitle>
                {isSuccess ? (
                    <Button>Вернуться на главную</Button>
                ) : (
                    <ButtonsContainer>
                        <Button>Попробовать снова</Button>
                        <Button isPlain to='/'>
                            Вернуться на главную
                        </Button>
                    </ButtonsContainer>
                )}
            </Content>
            <Bg isSuccess={isSuccess}>
                <BgImage />
            </Bg>
        </Container>
    );
};

const Container = styled.div`
    margin-top: 24px;
    text-align: center;
    padding: 40px 48px 48px;
    background-color: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 10px;
    position: relative;
    overflow: hidden;
`;

const Content = styled.div`
    position: relative;
    z-index: 1;
`;

const IconContainer = styled(Icon)<{isSuccess: boolean}>`
    color: ${({theme, isSuccess}) => (isSuccess ? theme.palette.primary : theme.palette.regular)};
`;

const Title = styled.div<{isSuccess: boolean}>`
    font-size: 32px;
    font-weight: 500;
    margin-bottom: 16px;
    margin-top: 30px;
    color: ${({theme, isSuccess}) => (isSuccess ? theme.palette.primary : theme.palette.regular)};
`;

const Subtitle = styled.div`
    font-size: 20px;
    margin-bottom: 32px;
    white-space: pre-wrap;
`;

const ButtonsContainer = styled.div`
    display: inline-grid;
    grid-gap: 20px;
`;

const Bg = styled.div<{isSuccess: boolean}>`
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    color: ${({theme, isSuccess}) => (isSuccess ? theme.palette.primary : theme.palette.regular)};

    svg {
        fill: currentColor;
        transform: translate(-50%, -50%);
        left: 50%;
        position: relative;
        top: 50%;
    }
`;
