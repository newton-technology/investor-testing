import React, {useEffect, useRef} from 'react';
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
    success: 'Уведомление об оценке результата\nскоро придет на вашу почту',
    fail: 'Попробуйте пройти ещё раз,\nколичество попыток неограниченно.',
};

const MoExSchoolLink = process.env.REACT_APP_MOEX_SCHOOL_FAILED_TEST_LINK;

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
                <IconContainer name={isSuccess ? 'check_circle' : 'cross'} size={46} isSuccess={isSuccess} />
                <Title isSuccess={isSuccess}>{isSuccess ? title.success : title.fail}</Title>
                <Subtitle>{isSuccess ? subtitle.success : subtitle.fail}</Subtitle>
                {!isSuccess && (
                    <Subtitle>
                        Подготовиться помогут материалы
                        <br />
                        <FeaturedLink href={MoExSchoolLink} target='_blank'>
                            Школы Московской Биржи
                        </FeaturedLink>
                        .
                    </Subtitle>
                )}
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

    ${({theme}) => theme.breakpoint('md')`
         padding-left: 48px;
         padding-right: 48px;
    `}
`;

const Content = styled.div`
    position: relative;
    z-index: 1;
`;

const IconContainer = styled(Icon)<{isSuccess: boolean}>`
    background-color: #fff;
    padding: 27px;
    color: ${({theme, isSuccess}) => (isSuccess ? theme.palette.primary : theme.palette.regular)};
    position: relative;

    &:before {
        background-color: ${({theme, isSuccess}) => (isSuccess ? theme.palette.primary : theme.palette.regular)};
        content: '';
        opacity: 0.2;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
    }
`;

const Title = styled.div<{isSuccess: boolean}>`
    font-size: 24px;
    font-weight: 500;
    margin-bottom: 16px;
    margin-top: 20px;
    color: ${({theme, isSuccess}) => (isSuccess ? theme.palette.primary : theme.palette.regular)};

    ${({theme}) => theme.breakpoint('md')`
        font-size: 32px;
        margin-top: 30px;
    `}
`;

const Subtitle = styled.div`
    margin-bottom: 32px;
    white-space: pre-wrap;

    ${({theme}) => theme.breakpoint('md')`
        font-size: 20px;
    `}
`;

const ButtonsContainer = styled.div`
    display: grid;
    grid-gap: 8px;

    ${({theme}) => theme.breakpoint('md')`
        display: inline-grid;
        grid-gap: 4px;
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

        ${({theme}) => theme.breakpoint('md')`
            max-width: none;
        `}
    }
`;

const FeaturedLink = styled.a`
    color: ${({theme}) => theme.palette.featured};
    text-decoration: underline;
`;
