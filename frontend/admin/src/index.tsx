import React from 'react';
import ReactDOM from 'react-dom';
import {BrowserRouter as Router} from 'react-router-dom';

import './index.css';
import App from './App';

const baseURL = process.env.REACT_APP_ADMIN_URL;

ReactDOM.render(
    <React.StrictMode>
        <Router basename={baseURL}>
            <App />
        </Router>
    </React.StrictMode>,
    document.getElementById('root'),
);
