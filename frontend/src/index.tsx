import React from 'react';
import ReactDOM from 'react-dom';
import {BrowserRouter as Router} from 'react-router-dom';

import './index.css';
import AdminApp from './AdminApp';
import App from './App';

const RenderApp = window.location.href.includes(process.env.REACT_APP_ADMIN_URL || '') ? AdminApp : App;

ReactDOM.render(
    <React.StrictMode>
        <Router>
            <RenderApp />
        </Router>
    </React.StrictMode>,
    document.getElementById('root'),
);
