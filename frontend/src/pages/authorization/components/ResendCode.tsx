import React from 'react';

import {useCountdown} from '../../../hooks/useCountdown';

interface IProps {
    sendCode: () => void;
}

export const ResendCode: React.FC<IProps> = ({sendCode}) => {
    const {countdown, formatedCountdown} = useCountdown();

    if (countdown <= 0) {
        return (
            <button type='button' onClick={sendCode}>
                Отправить код
            </button>
        );
    }

    return <div>Выслать код повторно через {formatedCountdown}</div>;
};
