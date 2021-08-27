import React from 'react';

import {ServerErrorMessage} from '../components/ServerErrorMessage';
import {Button} from '../components/Button';

export const PageNotFound = () => {
    return (
        <ServerErrorMessage title='Ошибка 404' subtitle='Запрашиваемая страница не найдена'>
            <Button to='/'>Перейти на главную</Button>
        </ServerErrorMessage>
    );
};
