import React, {useEffect, useRef} from 'react';
import ReactDOM from 'react-dom';

export const Portal: React.FC = ({children}) => {
    const portalElemRef = useRef(document.createElement('div'));

    useEffect(() => {
        const portalElem = portalElemRef.current;
        document.body.appendChild(portalElem);

        return () => {
            portalElem.remove();
        };
    }, []);

    return ReactDOM.createPortal(children, portalElemRef.current);
};
