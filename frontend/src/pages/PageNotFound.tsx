import React from 'react';

import {ErrorMessage} from '../components/ErrorMessage';
import {Button} from '../components/Button';

export const PageNotFound = () => {
    return (
        <ErrorMessage title='Ошибка 404' subtitle='Запрашиваемая страница не найдена'>
            <Button to='/'>Перейти на главную</Button>
        </ErrorMessage>
    );
};
