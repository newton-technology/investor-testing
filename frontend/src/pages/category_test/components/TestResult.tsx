import React, {useEffect, useRef} from 'react';
import styled from 'styled-components';

import {Button} from '../../../components/Button';
import {Icon} from '../../../components/Icon';
import {ReactComponent as BgImage} from './result_bg.svg';
import {breakpoint} from '../../../theme/breakpont';

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
    const ref = useRef<HTMLDivElement>();

    useEffect(() => {
        if (ref.current) {
            ref.current.scrollIntoView({block: 'center', behavior: 'smooth'});
        }
    }, []);

    const refreshTest = () => {
        window.location.reload();
    };

    return (
        <Container ref={ref}>
            <Content>
                <IconContainer name='planet' size={112} isSuccess={isSuccess} />
                <Title isSuccess={isSuccess}>{isSuccess ? title.success : title.fail}</Title>
                <Subtitle>{isSuccess ? subtitle.success : subtitle.fail}</Subtitle>
                {isSuccess ? (
                    <Button to='/'>Вернуться на главную</Button>
                ) : (
                    <ButtonsContainer>
                        <Button onClick={refreshTest}>Попробовать снова</Button>
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

const Container = styled.div<{ref: any}>`
    text-align: center;
    padding: 40px 24px 48px;
    background-color: ${({theme}) => theme.palette.bg.secondary};
    border-radius: 10px;
    position: relative;
    overflow: hidden;
    max-width: 566px;
    margin: 24px auto 0;

    ${breakpoint('md')`
         padding-left: 48px;
         padding-right: 48px;
    `}
`;

const Content = styled.div`
    position: relative;
    z-index: 1;
`;

const IconContainer = styled(Icon)<{isSuccess: boolean}>`
    color: ${({theme, isSuccess}) => (isSuccess ? theme.palette.primary : theme.palette.regular)};

    svg {
        ${breakpoint('md')`
            width: 142px;
            height: 142px;
        `}
    }
`;

const Title = styled.div<{isSuccess: boolean}>`
    font-size: 24px;
    font-weight: 500;
    margin-bottom: 16px;
    margin-top: 20px;
    color: ${({theme, isSuccess}) => (isSuccess ? theme.palette.primary : theme.palette.regular)};

    ${breakpoint('md')`
        font-size: 32px;
        margin-top: 30px;
    `}
`;

const Subtitle = styled.div`
    margin-bottom: 32px;
    white-space: pre-wrap;

    ${breakpoint('md')`
        font-size: 20px;
    `}
`;

const ButtonsContainer = styled.div`
    display: grid;
    grid-gap: 8px;

    ${breakpoint('md')`
        display: inline-grid;
        grid-gap: 20px;
    `}
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
        max-width: 120%;

        ${breakpoint('md')`
            max-width: none;
        `}
    }
`;
