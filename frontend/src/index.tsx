import React from 'react';
import ReactDOM from 'react-dom';

import './index.css';

const importBuildTarget = (): any => {
    if (process.env.REACT_APP_BUILD_TARGET === 'client') {
        return import('./App');
    } else if (process.env.REACT_APP_BUILD_TARGET === 'admin') {
        return import('./Admin');
    } else {
        return Promise.reject(new Error('No such build target: ' + process.env.REACT_APP_BUILD_TARGET));
    }
};

(async () => {
    const {default: App} = await importBuildTarget();
    ReactDOM.render(
        <React.StrictMode>
            <App />
        </React.StrictMode>,
        document.getElementById('root'),
    );
})();
