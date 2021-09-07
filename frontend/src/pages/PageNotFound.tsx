import React from 'react';

import {Button} from '../components/Button';
import {ErrorMessage} from '../components/ErrorMessage';

export const PageNotFound = () => {
    return (
        <ErrorMessage title='Ошибка 404' subtitle='Запрашиваемая страница не найдена'>
            <Button to='/'>Перейти на главную</Button>
        </ErrorMessage>
    );
};
