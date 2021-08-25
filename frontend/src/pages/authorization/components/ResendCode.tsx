import React, {useEffect, useRef, useState} from 'react';

import {useCountdown} from '../../../hooks/useCountdown';

export const ResendCode: React.FC = () => {
    const {countdown, formatedCountdown} = useCountdown(5000);

    if (countdown <= 0) {
        return <div>Отправить код</div>;
    }

    return <div>{formatedCountdown}</div>;
};
